<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Category;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::with('category')->orderBy('id', 'desc')->get();
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.ads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'media_type' => 'required|in:image,video',
            'media' => 'nullable|file|max:102400', // 100MB
            'media_url' => 'nullable|url',
            'link_url' => 'required|url',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only('title', 'media_type', 'link_url', 'category_id');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('ads', 'public');
            $data['media_path'] = '/storage/' . $path;
        } elseif ($request->media_url) {
            $data['media_url'] = $request->media_url;
        }

        Ad::create($data);

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement created successfully');
    }

    public function edit(Ad $ad)
    {
        $categories = Category::all();
        return view('admin.ads.edit', compact('ad', 'categories'));
    }

    public function update(Request $request, Ad $ad)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'media_type' => 'required|in:image,video',
            'media' => 'nullable|file|max:102400',
            'media_url' => 'nullable|url',
            'link_url' => 'required|url',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only('title', 'media_type', 'link_url', 'category_id');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('media')) {
            // Delete old media
            if ($ad->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $path = $request->file('media')->store('ads', 'public');
            $data['media_path'] = '/storage/' . $path;
            $data['media_url'] = null;
        } elseif ($request->media_url) {
            if ($ad->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = $request->media_url;
        } elseif ($request->boolean('remove_media')) {
            if ($ad->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = null;
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement updated successfully');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->getRawOriginal('media_path')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $ad->getRawOriginal('media_path')));
        }
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Advertisement deleted successfully');
    }

    /**
     * Toggle the active status of an ad.
     */
    public function toggleStatus(Ad $ad)
    {
        $ad->update(['is_active' => !$ad->is_active]);
        return redirect()->route('admin.ads.index')->with('success', 'Advertisement status updated');
    }
}
