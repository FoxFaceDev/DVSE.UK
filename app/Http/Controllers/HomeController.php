<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Section;

class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('home', compact('sections'));
    }
}
