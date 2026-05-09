<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Category;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $questions = Question::with('category')
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.questions.index', compact('questions', 'categories'));
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
            'media_type' => 'nullable|in:image,video,gif',
            'media' => 'nullable|file|max:102400', // 100MB max
            'media_url' => 'nullable|url',
            'explanation_en' => 'nullable|string',
            'explanation_ku' => 'nullable|string',
            'choices' => 'required|array|min:4',
            'correct_choice' => 'required|numeric'
        ]);

        $data = $request->except('media', 'media_url', 'choices', 'correct_choice');

        // Handle media upload
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('questions', 'public');
            $data['media_path'] = '/storage/' . $path;
            // Auto-detect media_type if not set
            if (!$request->media_type) {
                $mime = $request->file('media')->getMimeType();
                if (str_starts_with($mime, 'video/')) {
                    $data['media_type'] = 'video';
                } elseif ($mime === 'image/gif') {
                    $data['media_type'] = 'gif';
                } else {
                    $data['media_type'] = 'image';
                }
            }
        } elseif ($request->media_url) {
            $data['media_url'] = $request->media_url;
            // Ensure media_type is set when using URL
            if (!$request->media_type) {
                $data['media_type'] = 'video'; // default for URLs
            }
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
            'media_type' => 'nullable|in:image,video,gif',
            'media' => 'nullable|file|max:102400', // 100MB max
            'media_url' => 'nullable|url',
            'explanation_en' => 'nullable|string',
            'explanation_ku' => 'nullable|string',
            'choices' => 'required|array|min:4',
            'correct_choice' => 'required|numeric'
        ]);

        $data = $request->except('media', 'media_url', 'choices', 'correct_choice', 'remove_media');

        if ($request->hasFile('media')) {
            // Delete old media if exists
            if ($question->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->getRawOriginal('media_path')));
            }
            $path = $request->file('media')->store('questions', 'public');
            $data['media_path'] = '/storage/' . $path;
            $data['media_url'] = null; // Clear URL when uploading file

            // Auto-detect media_type if not set
            if (!$request->media_type) {
                $mime = $request->file('media')->getMimeType();
                if (str_starts_with($mime, 'video/')) {
                    $data['media_type'] = 'video';
                } elseif ($mime === 'image/gif') {
                    $data['media_type'] = 'gif';
                } else {
                    $data['media_type'] = 'image';
                }
            }
        } elseif ($request->media_url) {
            // Using URL - clear uploaded file
            if ($question->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = $request->media_url;
        } elseif ($request->boolean('remove_media')) {
            if ($question->getRawOriginal('media_path')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->getRawOriginal('media_path')));
            }
            $data['media_path'] = null;
            $data['media_url'] = null;
            $data['media_type'] = null;
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
        if ($question->getRawOriginal('media_path')) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $question->getRawOriginal('media_path')));
        }
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully');
    }
}
