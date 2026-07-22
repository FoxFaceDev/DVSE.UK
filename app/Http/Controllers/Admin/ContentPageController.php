<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentPageRequest;
use App\Models\Category;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentPageController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name_en')->get();
        $contentPages = ContentPage::with(['category', 'clips'])
            ->when($request->category_id, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->latest('id')
            ->get();

        return view('admin.content_pages.index', compact('contentPages', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = Category::orderBy('name_en')->get();
        $selectedCategoryId = $request->query('category_id');
        $selectedType = $request->query('type', ContentPage::TYPE_CGI_CLIPS);

        return view('admin.content_pages.create', compact('categories', 'selectedCategoryId', 'selectedType'));
    }

    public function store(ContentPageRequest $request)
    {
        $contentPage = ContentPage::create($this->pageData($request));

        if ($contentPage->type === ContentPage::TYPE_CGI_CLIPS) {
            $this->syncClips($request, $contentPage);
        } else {
            $this->storeSignImage($request, $contentPage);
        }

        return redirect()->route('admin.content-pages.index')
            ->with('success', 'Learning page added successfully.');
    }

    public function edit(ContentPage $contentPage)
    {
        $categories = Category::orderBy('name_en')->get();
        $contentPage->load('clips');

        return view('admin.content_pages.edit', compact('contentPage', 'categories'));
    }

    public function update(ContentPageRequest $request, ContentPage $contentPage)
    {
        $newType = $request->input('type');

        if ($newType === ContentPage::TYPE_CGI_CLIPS) {
            $this->deleteStoredFile($contentPage->getRawOriginal('sign_image_path'));
            $contentPage->fill($this->pageData($request));
            $contentPage->sign_image_path = null;
            $contentPage->explanation_en = null;
            $contentPage->explanation_ku = null;
            $contentPage->save();
            $this->syncClips($request, $contentPage);
        } else {
            $this->deleteAllClips($contentPage);
            $contentPage->fill($this->pageData($request));
            $contentPage->text_en = null;
            $contentPage->text_ku = null;
            $contentPage->save();
            $this->storeSignImage($request, $contentPage);
        }

        return redirect()->route('admin.content-pages.index')
            ->with('success', 'Learning page updated successfully.');
    }

    public function destroy(ContentPage $contentPage)
    {
        $this->deleteStoredFile($contentPage->getRawOriginal('sign_image_path'));
        $this->deleteAllClips($contentPage);
        $contentPage->delete();

        return redirect()->route('admin.content-pages.index')
            ->with('success', 'Learning page deleted successfully.');
    }

    private function pageData(ContentPageRequest $request): array
    {
        $isCgiClips = $request->input('type') === ContentPage::TYPE_CGI_CLIPS;
        $hazardWindows = $isCgiClips
            ? collect($request->input('hazard_windows', []))
                ->map(fn ($window) => [
                    'start' => (float) $window['start'],
                    'end' => (float) $window['end'],
                    'points' => (int) $window['points'],
                ])
                ->values()
                ->all()
            : null;
        $firstHazardWindow = $hazardWindows[0] ?? null;

        return [
            'category_id' => $request->integer('category_id'),
            'type' => $request->input('type'),
            'text_en' => $request->input('type') === ContentPage::TYPE_CGI_CLIPS
                ? $request->input('text_en')
                : null,
            'text_ku' => $request->input('type') === ContentPage::TYPE_CGI_CLIPS
                ? $request->input('text_ku')
                : null,
            // Keep the original columns synchronized for backward compatibility.
            'hazard_window_start' => $firstHazardWindow['start'] ?? null,
            'hazard_window_end' => $firstHazardWindow['end'] ?? null,
            'hazard_windows' => $hazardWindows,
            'explanation_en' => $request->input('type') === ContentPage::TYPE_MOTORWAY_SIGN
                ? $request->input('explanation_en')
                : null,
            'explanation_ku' => $request->input('type') === ContentPage::TYPE_MOTORWAY_SIGN
                ? $request->input('explanation_ku')
                : null,
        ];
    }

    private function syncClips(ContentPageRequest $request, ContentPage $contentPage): void
    {
        $existingClips = $contentPage->clips()->get()->keyBy('slot');

        foreach (range(0, 1) as $slot) {
            $existingClip = $existingClips->get($slot);
            $uploadedClip = $request->file("clips.$slot.media");
            $removeClip = $request->boolean("clips.$slot.remove");

            if ($uploadedClip) {
                if ($existingClip) {
                    $this->deleteStoredFile($existingClip->getRawOriginal('media_path'));
                }

                $path = $uploadedClip->store('content-pages/cgi', 'public');
                $contentPage->clips()->updateOrCreate(
                    ['slot' => $slot],
                    ['media_path' => '/storage/'.$path, 'media_url' => null]
                );

                continue;
            }

            if ($removeClip && $existingClip) {
                $this->deleteStoredFile($existingClip->getRawOriginal('media_path'));
                $existingClip->delete();
            }
        }

        $contentPage->clips()
            ->whereNotIn('slot', [0, 1])
            ->get()
            ->each(function ($clip) {
                $this->deleteStoredFile($clip->getRawOriginal('media_path'));
                $clip->delete();
            });
    }

    private function storeSignImage(ContentPageRequest $request, ContentPage $contentPage): void
    {
        if (! $request->hasFile('sign_image')) {
            return;
        }

        $this->deleteStoredFile($contentPage->getRawOriginal('sign_image_path'));
        $path = $request->file('sign_image')->store('content-pages/signs', 'public');
        $contentPage->update(['sign_image_path' => '/storage/'.$path]);
    }

    private function deleteAllClips(ContentPage $contentPage): void
    {
        $contentPage->clips()->get()->each(function ($clip) {
            $this->deleteStoredFile($clip->getRawOriginal('media_path'));
            $clip->delete();
        });
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $path), '/'));
    }
}
