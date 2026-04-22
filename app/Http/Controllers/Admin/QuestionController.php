<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Category;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('category')->get();
        return view('admin.questions.index', compact('questions'));
    }

    public function create(\Illuminate\Http\Request $request)
    {
        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id');
        return view('admin.questions.create', compact('categories', 'selectedCategoryId'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'text_en' => 'required|string',
            'text_ku' => 'nullable|string',
            'image' => 'nullable|image',
            'explanation_en' => 'nullable|string',
            'explanation_ku' => 'nullable|string',
            'choices' => 'required|array|min:4',
            'correct_choice' => 'required|numeric'
        ]);

        $data = $request->except('image', 'choices', 'correct_choice');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = '/storage/' . $path;
        }

        $question = Question::create($data);

        foreach ($request->choices as $index => $choiceData) {
            $question->choices()->create([
                'text_en' => $choiceData['text_en'],
                'text_ku' => $choiceData['text_ku'] ?? null,
                'is_correct' => ($index == $request->correct_choice)
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question added successfully');
    }
}
