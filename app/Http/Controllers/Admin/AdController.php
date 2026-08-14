<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Language;
use App\Services\AdLifecycleNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString();
        $ads = Ad::query()
            ->with(['categories', 'language'])
            ->when($search, function ($query, $term) {
                $query->where(function ($searchQuery) use ($term) {
                    $searchQuery->where('title', 'like', "%{$term}%")
                        ->orWhere('link_url', 'like', "%{$term}%")
                        ->orWhereHas('language', fn ($languageQuery) => $languageQuery->where('name', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%"))
                        ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('name_en', 'like', "%{$term}%"));
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        $categories = Category::with('subSection')->orderBy('name_en')->get();
        $languages = Language::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ads.create', compact('categories', 'languages') + ['placementOptions' => $this->placementOptions()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language_id' => 'nullable|integer|exists:languages,id',
            'display_type' => 'required|in:question,site',
            'placements' => 'nullable|array',
            'placements.*' => 'string|in:home,sections,subsections,categories,about,contact,account,mock_tests',
            'media_type' => 'required|in:image,video',
            'media' => 'nullable|file|max:102400', // 100MB
            'media_url' => 'nullable|url',
            'link_url' => 'required|url',
            'advertiser_email' => 'required|email|max:255',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'target_all_categories' => 'required|boolean',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|distinct|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $this->ensureCategoriesSelected($request);

        $data = $request->only('title', 'language_id', 'display_type', 'placements', 'media_type', 'link_url', 'advertiser_email', 'starts_at', 'expires_at');
        $data['placements'] = $request->input('display_type') === 'site' ? array_values($validated['placements'] ?? []) : null;
        $data['targets_all_categories'] = $request->boolean('target_all_categories');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('ads', 'public');
            $data['media_path'] = '/storage/'.$path;
        } elseif ($request->media_url) {
            $data['media_url'] = $request->media_url;
        }

        $ad = Ad::create($data);
        $this->syncCategories($ad, $validated['category_ids'] ?? []);
        $notifier = app(AdLifecycleNotifier::class);
        $notifier->notifyActivationIfDue($ad);

        return $this->advertisementRedirect('Advertisement created successfully', $notifier);
    }

    public function edit(Ad $ad)
    {
        $categories = Category::with('subSection')->orderBy('name_en')->get();
        $languages = Language::orderBy('sort_order')->orderBy('name')->get();
        $ad->load(['categories', 'language']);

        return view('admin.ads.edit', compact('ad', 'categories', 'languages') + ['placementOptions' => $this->placementOptions()]);
    }

    public function update(Request $request, Ad $ad)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'language_id' => 'nullable|integer|exists:languages,id',
            'display_type' => 'required|in:question,site',
            'placements' => 'nullable|array',
            'placements.*' => 'string|in:home,sections,subsections,categories,about,contact,account,mock_tests',
            'media_type' => 'required|in:image,video',
            'media' => 'nullable|file|max:102400',
            'media_url' => 'nullable|url',
            'link_url' => 'required|url',
            'advertiser_email' => 'required|email|max:255',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'target_all_categories' => 'required|boolean',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|distinct|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $this->ensureCategoriesSelected($request);

        $data = $request->only('title', 'language_id', 'display_type', 'placements', 'media_type', 'link_url', 'advertiser_email', 'starts_at', 'expires_at');
        $data['placements'] = $request->input('display_type') === 'site' ? array_values($validated['placements'] ?? []) : null;
        $data['targets_all_categories'] = $request->boolean('target_all_categories');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('media')) {
            // Delete old media
            if ($ad->getRawOriginal('media_path')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $path = $request->file('media')->store('ads', 'public');
            $data['media_path'] = '/storage/'.$path;
            $data['media_url'] = null;
        } elseif ($request->media_url) {
            if ($ad->getRawOriginal('media_path')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = $request->media_url;
        } elseif ($request->boolean('remove_media')) {
            if ($ad->getRawOriginal('media_path')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = null;
        }

        $ad->update($data);
        $this->syncCategories($ad, $validated['category_ids'] ?? []);
        $notifier = app(AdLifecycleNotifier::class);
        $notifier->notifyActivationIfDue($ad);

        return $this->advertisementRedirect('Advertisement updated successfully', $notifier);
    }

    public function destroy(Ad $ad)
    {
        if ($ad->getRawOriginal('media_path')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
        }
        $ad->delete();

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement deleted successfully');
    }

    /**
     * Toggle the active status of an ad.
     */
    public function toggleStatus(Ad $ad)
    {
        $ad->update(['is_active' => ! $ad->is_active]);
        $notifier = app(AdLifecycleNotifier::class);
        if ($ad->is_active) {
            $notifier->notifyActivationIfDue($ad);
        }

        return $this->advertisementRedirect('Advertisement status updated', $notifier);
    }

    private function ensureCategoriesSelected(Request $request): void
    {
        if ($request->input('display_type') === 'site' && empty($request->input('placements', []))) {
            throw ValidationException::withMessages(['placements' => 'Select at least one website placement.']);
        }
        if ($request->input('display_type', 'question') === 'question' && ! $request->boolean('target_all_categories') && empty($request->input('category_ids', []))) {
            throw ValidationException::withMessages([
                'category_ids' => 'Select at least one category or choose Select all categories.',
            ]);
        }
    }

    private function syncCategories(Ad $ad, array $categoryIds): void
    {
        $ad->categories()->sync(
            $ad->display_type === 'site' || $ad->targets_all_categories ? [] : collect($categoryIds)->map(fn ($id) => (int) $id)->unique()->all()
        );
    }

    private function placementOptions(): array
    {
        return ['home' => 'Home page', 'sections' => 'Section pages', 'subsections' => 'Subsection pages', 'categories' => 'Category pages', 'about' => 'About us', 'contact' => 'Contact us', 'account' => 'Account pages', 'mock_tests' => 'Mock-test information and results'];
    }

    private function advertisementRedirect(string $message, AdLifecycleNotifier $notifier)
    {
        $response = redirect()->route('admin.ads.index')->with('success', $message);

        if ($notifier->lastFailure()) {
            $response->with('warning', 'The advertisement was saved, but its activation email could not be sent. Check the SMTP hostname and certificate settings. The scheduled notification task will retry it automatically.');
        }

        return $response;
    }
}
