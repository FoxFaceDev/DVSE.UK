<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdvertisementEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailAdvertisementController extends Controller
{
    public function create()
    {
        return view('admin.email_advertisements.create', [
            'recipientCounts' => [
                'all' => User::count(),
                User::ACCOUNT_TYPE_USER => User::where('account_type', User::ACCOUNT_TYPE_USER)->count(),
                User::ACCOUNT_TYPE_INSTRUCTOR => User::where('account_type', User::ACCOUNT_TYPE_INSTRUCTOR)->count(),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'audience' => ['required', 'in:all,user,instructor'],
            'subject' => ['required', 'string', 'max:150'],
            'headline' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:20000'],
            'button_label' => ['nullable', 'required_with:link_url', 'string', 'max:40'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('email-advertisements', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        $recipients = $this->recipients($validated['audience']);
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
                ));
                $sent++;
            }
        });

        return redirect()->route('admin.email-advertisements.create')
            ->with('success', "Advertisement email sent to {$sent} ".str('recipient')->plural($sent).'.');
    }

    private function recipients(string $audience): Builder
    {
        return User::query()
            ->when($audience !== 'all', fn (Builder $query) => $query->where('account_type', $audience))
            ->orderBy('id');
    }
}
