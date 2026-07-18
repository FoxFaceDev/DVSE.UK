<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\SubSection;

class FrontendController extends Controller
{
    public function showSection(Section $section)
    {
        $section->load('subSections');
        return view('frontend.section', compact('section'));
    }

    public function showSubSection(SubSection $subSection)
    {
        $subSection->load(['categories' => function($query) {
            $query->withCount(['questions', 'contentPages']);
        }]);
        return view('frontend.subsection', compact('subSection'));
    }
}
