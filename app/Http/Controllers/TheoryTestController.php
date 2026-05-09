<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Ad;

class TheoryTestController extends Controller
{
    public function practice(Category $category)
    {
        $questions = $category->questions()->with('choices')->get();

        // Fetch an active ad for this category (or global ad)
        $ad = Ad::where('is_active', true)
            ->where(function ($query) use ($category) {
                $query->where('category_id', $category->id)
                      ->orWhereNull('category_id');
            })
            ->inRandomOrder()
            ->first();

        return view('theory.practice', compact('category', 'questions', 'ad'));
    }

    public function result()
    {
        return view('theory.result');
    }
}
