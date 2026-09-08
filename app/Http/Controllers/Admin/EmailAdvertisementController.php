<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdvertisementEmail;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class EmailAdvertisementController extends Controller
{
    public function create()
    {
        $subscribers = User::eligibleForMarketing();

        return view('admin.email_advertisements.create', [
            'languages' => Language::active()->get(),
            'recipientCounts' => [
                'all' => (clone $subscribers)->count(),
                User::ACCOUNT_TYPE_USER => (clone $subscribers)->where('account_type', User::ACCOUNT_TYPE_USER)->count(),
                User::ACCOUNT_TYPE_INSTRUCTOR => (clone $subscribers)->where('account_type', User::ACCOUNT_TYPE_INSTRUCTOR)->count(),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'audience' => ['required', 'in:all,user,instructor'],
            'language_ids' => ['nullable', 'array', 'min:1'],
            'language_ids.*' => ['integer', 'distinct', 'exists:languages,id'],
            'language_filter_present' => ['nullable', 'boolean'],
            'subject' => ['required', 'string', 'max:150'],
            'headline' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:20000'],
            'button_label' => ['nullable', 'required_with:link_url', 'string', 'max:40'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'business_name' => ['required', 'string', 'max:150'],
            'business_address' => ['required', 'string', 'max:500'],
            'contact_email' => ['required', 'email', 'max:255'],
        ]);

        if ($request->boolean('language_filter_present') && empty($validated['language_ids'] ?? [])) {
            throw ValidationException::withMessages(['language_ids' => 'Select at least one recipient language.']);
        }

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('email-advertisements', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        $recipients = $this->recipients($validated['audience'], $validated['language_ids'] ?? null);
        $sent = 0;

        $recipients->chunkById(100, function ($users) use ($validated, $imageUrl, &$sent) {
            foreach ($users as $user) {
                Mail::to($user)->send(new AdvertisementEmail(
                    recipientName: $user->name,
                    subjectLine: $validated['subject'],
                    headline: $validated['headline'],
                    messageBody: $validated['message'],
                    buttonLabel: $validated['button_label'] ?? null,
                    linkUrl: $validated['link_url'] ?? null,
                    imageUrl: $imageUrl,
                    businessName: $validated['business_name'],
                    businessAddress: $validated['business_address'],
                    contactEmail: $validated['contact_email'],
                    unsubscribeUrl: URL::signedRoute('marketing.unsubscribe.show', ['user' => $user]),
                ));
                $sent++;
            }
        });

        return redirect()->route('admin.email-advertisements.create')
            ->with('success', "Advertisement email sent to {$sent} ".str('recipient')->plural($sent).'.');
    }

    private function recipients(string $audience, ?array $languageIds = null): Builder
    {
        return User::query()
            ->eligibleForMarketing()
            ->when($audience !== 'all', fn (Builder $query) => $query->where('account_type', $audience))
            ->when($languageIds !== null, fn (Builder $query) => $query->whereIn('preferred_language_id', $languageIds))
            ->orderBy('id');
    }
}
