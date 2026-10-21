<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use App\Support\RevisionCatalog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // The index is essentially handled by sub_sections.show now, but we can keep it if needed.
    // I will redirect to sub_sections.show if index is called directly.
    public function index(Section $section, SubSection $subSection)
    {
        return redirect()->route('admin.sections.sub_sections.show', [$section, $subSection]);
    }

    public function create(Section $section, SubSection $subSection)
    {
        return view('admin.categories.create', compact('section', 'subSection'));
    }

    public function show(Section $section, SubSection $subSection, Category $category)
    {
        $category->load('topics');

        return view('admin.categories.show', compact('section', 'subSection', 'category'));
    }

    public function store(Request $request, Section $section, SubSection $subSection)
    {
        $validated = $request->validate(['name_en' => 'required|string|max:255', 'name_ku' => 'nullable|string|max:255', 'page_type' => 'nullable|in:revision,standard']);

        $category = $subSection->categories()->create($validated);
        if ($category->isRevisionPractice()) {
            $category->update(['page_type' => 'revision']);
            RevisionCatalog::populate($category);
        }

        return redirect()->route('admin.sections.sub_sections.show', [$section, $subSection])
            ->with('success', 'Category added');
    }

    public function edit(Section $section, SubSection $subSection, Category $category)
    {
        return view('admin.categories.edit', compact('section', 'subSection', 'category'));
    }

    public function update(Request $request, Section $section, SubSection $subSection, Category $category)
    {
        $wasRevision = $category->isRevisionPractice();
        $validated = $request->validate(['name_en' => 'required|string|max:255', 'name_ku' => 'nullable|string|max:255', 'page_type' => 'nullable|in:revision,standard']);
        if (! $request->exists('page_type') && $wasRevision) {
            $validated['page_type'] = 'revision';
        }
        $category->update($validated);
        if (! $wasRevision && $category->isRevisionPractice()) {
            $category->update(['page_type' => 'revision']);
            RevisionCatalog::populate($category);
        }

        return redirect()->route('admin.sections.sub_sections.show', [$section, $subSection])
            ->with('success', 'Category updated');
    }

    public function destroy(Section $section, SubSection $subSection, Category $category)
    {
        $category->delete();

        return redirect()->route('admin.sections.sub_sections.show', [$section, $subSection])
            ->with('success', 'Category deleted');
    }
}
