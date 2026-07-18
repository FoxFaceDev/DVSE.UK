<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Support\Facades\Storage;

class SubSectionController extends Controller
{
    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|max:2048',
        ]);

        $subSection = new SubSection();
        $subSection->section_id = $section->id;
        $subSection->name = $validated['name'];
        $subSection->color = $validated['color'] ?? '#3b82f6';

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $subSection->icon_path = '/storage/' . $path;
        }

        $subSection->save();

        return redirect()->route('admin.sections.show', $section)->with('success', 'Sub-section created successfully.');
    }

    public function show(Section $section, SubSection $subSection)
    {
        $subSection->load(['categories' => function($query) {
            $query->withCount(['questions', 'contentPages']);
        }]);
        
        return view('admin.sub_sections.show', compact('section', 'subSection'));
    }

    public function update(Request $request, Section $section, SubSection $subSection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|image|max:2048',
        ]);

        $subSection->name = $validated['name'];
        if (isset($validated['color'])) {
            $subSection->color = $validated['color'];
        }

        if ($request->hasFile('icon')) {
            if ($subSection->icon_path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $subSection->icon_path));
            }
            $path = $request->file('icon')->store('icons', 'public');
            $subSection->icon_path = '/storage/' . $path;
        }

        $subSection->save();

        return redirect()->route('admin.sections.show', $section)->with('success', 'Sub-section updated successfully.');
    }

    public function destroy(Section $section, SubSection $subSection)
    {
        if ($subSection->icon_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $subSection->icon_path));
        }
        $subSection->delete();
        return redirect()->route('admin.sections.show', $section)->with('success', 'Sub-section deleted successfully.');
    }
}
