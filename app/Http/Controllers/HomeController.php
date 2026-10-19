<?php

namespace App\Http\Controllers;

use App\Models\Section;

class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        $theorySection = $sections->first(fn ($section) => str_contains(strtolower($section->name), 'theory'));

        return view('home', compact('sections', 'theorySection'));
    }
}
