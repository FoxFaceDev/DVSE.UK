<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            $query->with('questions:id,topic_id')->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);

        return view(str_contains(strtolower($section->name), 'theory') ? 'v2.theory' : 'frontend.section', compact('section'));
    }

    public function showSubSection(SubSection $subSection, Request $request)
    {
        if ($subSection->isHazardPerception()) {
            return app(LearnerController::class)->hazardPage($request, $subSection);
        }

        if ($subSection->isRevisionPractice()) {
            return redirect()->route('v2.revision');
        }

        $subSection->load(['section', 'categories' => fn ($query) => $query->withCount('topics'), 'mockTests' => fn ($query) => $query->where('is_active', true), 'topics' => function ($query) {
            $query->with('questions:id,topic_id')->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);

        if (Str::contains(Str::lower($subSection->name), ['theory test', 'theory-test'])) {
            return view('v2.theory-menu', compact('subSection'));
        }

        return view('frontend.subsection', compact('subSection'));
    }

    public function showCategory(Category $category, Request $request)
    {
        if ($category->isRevisionPractice()) {
            return app(LearnerController::class)->revision($request, $category);
        }

        $category->load(['subSection.section', 'topics' => function ($query) {
            $query->with('questions:id,topic_id')->withCount(['questions', 'contentPages', 'contentPages as cgi_content_pages_count' => fn ($q) => $q->where('type', 'cgi_clips')]);
        }]);

        return view('frontend.category', compact('category'));
    }
}
