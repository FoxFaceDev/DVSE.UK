<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Category;

class FrontendController extends Controller
{
    public function showSection(Section $section)
    {
        $section->load(['subSections', 'topics' => function($query) {
            $query->withCount(['questions', 'contentPages']);
        }]);
        return view('frontend.section', compact('section'));
    }

    public function showSubSection(SubSection $subSection)
    {
        $subSection->load(['categories', 'topics' => function($query) {
            $query->withCount(['questions', 'contentPages']);
        }]);
        return view('frontend.subsection', compact('subSection'));
    }

    public function showCategory(Category $category)
    {
        $category->load(['topics' => function($query) {
            $query->withCount(['questions', 'contentPages']);
        }]);
        return view('frontend.category', compact('category'));
    }
}
