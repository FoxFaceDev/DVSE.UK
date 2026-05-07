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

    public function edit(Question $question)
    {
        $categories = Category::all();
        $question->load('choices');
        return view('admin.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
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

        $data = $request->except('image', 'choices', 'correct_choice', 'remove_image');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($question->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->image_path));
            }
            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = '/storage/' . $path;
        } elseif ($request->boolean('remove_image')) {
            if ($question->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->image_path));
            }
            $data['image_path'] = null;
        }

        $question->update($data);

        // Update choices
        foreach ($request->choices as $index => $choiceData) {
            $choice = $question->choices()->updateOrCreate(
                ['id' => $choiceData['id'] ?? null],
                [
                    'text_en' => $choiceData['text_en'],
                    'text_ku' => $choiceData['text_ku'] ?? null,
                    'is_correct' => ($index == $request->correct_choice)
                ]
            );
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully');
    }

    public function destroy(Question $question)
    {
        if ($question->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->image_path));
        }
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully');
    }
}
