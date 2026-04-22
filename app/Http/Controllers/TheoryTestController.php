<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

class TheoryTestController extends Controller
{
    public function index()
    {
        return view('theory.index');
    }

    public function categories()
    {
        $categories = Category::withCount('questions')->get();
        return view('theory.categories', compact('categories'));
    }

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
