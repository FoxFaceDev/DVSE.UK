<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Section;

class AdminController extends Controller
{
    public function index()
    {
        $sections = Section::withCount('subSections')->get();
        return view('admin.home', compact('sections'));
    }
}
