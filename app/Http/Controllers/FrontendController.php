<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;

class FrontendController extends Controller
{
    public function learn()
    {
        $sections = Section::with('subSections')->get();

        return view('frontend.learn', compact('sections'));
    }

    public function showSection(Section $section)
    {
        $section->load(['subSections', 'topics' => function ($query) {
            $query->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);
        return view('frontend.section', compact('section'));
    }

    public function showSubSection(SubSection $subSection)
    {
        $subSection->load(['section', 'categories' => fn ($query) => $query->withCount('topics'), 'mockTests' => fn ($query) => $query->where('is_active', true), 'topics' => function ($query) {
            $query->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);
        return view('frontend.subsection', compact('subSection'));
    }

    public function showCategory(Category $category)
    {
        $category->load(['subSection.section', 'topics' => function ($query) {
            $query->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);

        return view('frontend.category', compact('category'));
    }
}
