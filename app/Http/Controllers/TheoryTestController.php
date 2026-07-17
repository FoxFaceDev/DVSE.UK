<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Ad;
use App\Models\Question;
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
        $questions = Question::with('choices')->inRandomOrder()->limit(50)->get();

        return view('theory.mock_test', compact('subSection', 'questions'));
    }

    public function mockTestResult(Request $request)
    {
        $correct = (int) $request->query('correct', 0);
        $total = (int) $request->query('total', 50);

        // Pass threshold is exactly 43 out of 50.
        $passed = $correct >= 43;

        return view('theory.mock_result', compact('correct', 'total', 'passed'));
    }

    public function submitMockTest(Request $request)
    {
        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1', 'max:50'],
            'question_ids.*' => ['integer', 'distinct', 'exists:questions,id'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['integer'],
        ]);

        $questionIds = collect($validated['question_ids'])->map(fn ($id) => (int) $id)->values();
        $questions = Question::with('choices')->whereIn('id', $questionIds)->get()->keyBy('id');
        $answers = collect($validated['answers'] ?? []);
        $correct = 0;

        foreach ($questionIds as $questionId) {
            $question = $questions->get($questionId);
            $choiceId = (int) ($answers->get((string) $questionId, 0));

            if ($question && $question->choices->contains(fn ($choice) => $choice->id === $choiceId && $choice->is_correct)) {
                $correct++;
            }
        }

        $total = $questionIds->count();
        $passed = $correct >= 43;

        if (auth('web')->check() && auth('web')->user()->hasVerifiedEmail()) {
            \App\Models\MockTestHistory::create([
                'user_id' => auth('web')->id(),
                'score' => $correct,
                'total_questions' => $total,
                'passed' => $passed,
            ]);
        }

        $redirect = route('theory.mock_test_result', [
            'correct' => $correct,
            'total' => $total,
        ]);

        return $request->expectsJson()
            ? response()->json(['redirect' => $redirect])
            : redirect()->to($redirect);
    }

    public function history()
    {
        $histories = \App\Models\MockTestHistory::where('user_id', auth('web')->id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('theory.history', compact('histories'));
    }
}
