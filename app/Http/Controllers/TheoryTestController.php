<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Ad;
use App\Models\SubSection;

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

    public function mockTestInfo(SubSection $subSection)
    {
        return view('theory.mock_info', compact('subSection'));
    }

    public function mockTestStart(SubSection $subSection)
    {
        // Get 50 random questions from the database for the official mock test
        $questions = \App\Models\Question::with('choices')->inRandomOrder()->limit(50)->get();

        return view('theory.mock_test', compact('subSection', 'questions'));
    }

    public function mockTestResult(Request $request)
    {
        $correct = (int) $request->query('correct', 0);
        $total = (int) $request->query('total', 50);
        
        // Pass threshold is exactly 43 out of 50
        $passed = $correct >= 43;

        // Save history if user is logged in
        if (auth('web')->check()) {
            \App\Models\MockTestHistory::create([
                'user_id' => auth('web')->id(),
                'score' => $correct,
                'total_questions' => $total,
                'passed' => $passed,
            ]);
        }
        
        return view('theory.mock_result', compact('correct', 'total', 'passed'));
    }

    public function history()
    {
        $histories = \App\Models\MockTestHistory::where('user_id', auth('web')->id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('theory.history', compact('histories'));
    }
}
