<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|max:2048',
        ]);

        $section = new Section();
        $section->name = $validated['name'];
        $section->color = $validated['color'] ?? '#3b82f6'; // default color

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $section->icon_path = '/storage/' . $path;
        }

        $section->save();

        return redirect()->route('admin.home')->with('success', 'Section created successfully.');
    }

    public function show(Section $section)
    {
        $section->load('subSections');
        return view('admin.sections.show', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|max:2048',
        ]);

        $section->name = $validated['name'];
        if (isset($validated['color'])) {
            $section->color = $validated['color'];
        }

        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($section->icon_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $section->icon_path));
            }
            $path = $request->file('icon')->store('icons', 'public');
            $section->icon_path = '/storage/' . $path;
        }

        $section->save();

        return redirect()->route('admin.home')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        if ($section->icon_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $section->icon_path));
        }
        $section->delete();
        return redirect()->route('admin.home')->with('success', 'Section deleted successfully.');
    }
}
