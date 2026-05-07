<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class TheoryTestController extends Controller
{
    public function practice(Category $category)
    {
        $questions = $category->questions()->with('choices')->get();
        return view('theory.practice', compact('category', 'questions'));
    }

    public function result()
    {
        return view('theory.result');
    }
}
