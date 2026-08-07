<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $topics = Topic::with('topicable')->withCount('questions')->orderBy('name_en')->get();
        $questions = Question::with('topic.topicable')
            ->when($request->filled('topic_id'), fn ($query) => $query->where('topic_id', $request->integer('topic_id')))
            ->latest('id')->get();

        return view('admin.questions.index', compact('questions', 'topics'));
    }

    public function create(Request $request)
    {
        return view('admin.questions.create', [
            'topics' => Topic::with('topicable')->get(),
            'languages' => Language::active()->get(),
            'selectedTopicId' => $request->query('topic_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);
        $data = $this->questionData($request, $validated);
        $this->storeQuestionMedia($request, $data);
        $question = Question::create($data);
        $this->syncChoices($request, $question);

        return redirect()->route('admin.questions.index')->with('success', 'Question added successfully.');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.edit', [
            'question' => $question->load('choices'),
            'topics' => Topic::with('topicable')->get(),
            'languages' => Language::active()->get(),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);
        $data = $this->questionData($request, $validated, $question);
        $this->storeQuestionMedia($request, $data, $question);
        $question->update($data);
        $this->syncChoices($request, $question);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $this->deleteFile($question->getRawOriginal('media_path'));
        $question->choices->each(fn ($choice) => $this->deleteFile($choice->getRawOriginal('image_path')));
        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully.');
    }

    private function validateQuestion(Request $request): array
    {
        $validated = $request->validate([
            'topic_id' => ['required', 'exists:topics,id'],
            'question_type' => ['required', Rule::in(['text', 'image_answers'])],
            'translations.en.text' => ['required', 'string'],
            'translations.*.text' => ['nullable', 'string'],
            'translations.*.explanation' => ['nullable', 'string'],
            'media_type' => ['nullable', Rule::in(['image', 'video', 'gif'])],
            'media' => ['nullable', 'file', 'max:102400'],
            'media_url' => ['nullable', 'url'],
            'choices' => ['required', 'array', 'size:4'],
            'choices.*.id' => ['nullable', 'integer', 'exists:choices,id'],
            'choices.*.translations.en.text' => [Rule::requiredIf($request->input('question_type') === 'text'), 'nullable', 'string'],
            'choices.*.translations.*.text' => ['nullable', 'string'],
            'choices.*.image' => ['nullable', 'image', 'max:10240'],
            'correct_choice' => ['required', 'integer', 'between:0,3'],
        ], [], ['translations.en.text' => 'English question']);

        if ($request->input('question_type') === 'image_answers') {
            foreach (range(0, 3) as $index) {
                if (! $request->hasFile("choices.$index.image") && ! $request->boolean("choices.$index.existing_image")) {
                    throw ValidationException::withMessages(["choices.$index.image" => 'Each image-answer choice needs an image.']);
                }
            }
        }

        return $validated;
    }

    private function questionData(Request $request, array $validated, ?Question $question = null): array
    {
        $translations = collect($validated['translations'] ?? [])->map(fn ($value) => [
            'text' => $value['text'] ?? null,
            'explanation' => $value['explanation'] ?? null,
        ])->all();
        $translations = array_replace($question?->translations ?? [], $translations);

        return [
            'topic_id' => $validated['topic_id'],
            'question_type' => $validated['question_type'],
            'translations' => $translations,
            'text_en' => data_get($translations, 'en.text'),
            'text_ku' => data_get($translations, 'ku.text'),
            'explanation_en' => data_get($translations, 'en.explanation'),
            'explanation_ku' => data_get($translations, 'ku.explanation'),
            'media_type' => $request->input('media_type'),
        ];
    }

    private function storeQuestionMedia(Request $request, array &$data, ?Question $question = null): void
    {
        if ($request->hasFile('media')) {
            $this->deleteFile($question?->getRawOriginal('media_path'));
            $data['media_path'] = '/storage/'.$request->file('media')->store('questions', 'public');
            $data['media_url'] = null;
            if (! $data['media_type']) {
                $mime = $request->file('media')->getMimeType();
                $data['media_type'] = str_starts_with($mime, 'video/') ? 'video' : ($mime === 'image/gif' ? 'gif' : 'image');
            }
        } elseif ($request->filled('media_url')) {
            $this->deleteFile($question?->getRawOriginal('media_path'));
            $data['media_path'] = null;
            $data['media_url'] = $request->input('media_url');
            $data['media_type'] ??= 'video';
        } elseif ($request->boolean('remove_media')) {
            $this->deleteFile($question?->getRawOriginal('media_path'));
            $data['media_path'] = $data['media_url'] = $data['media_type'] = null;
        }
    }

    private function syncChoices(Request $request, Question $question): void
    {
        foreach (($request->all('choices')['choices'] ?? []) as $index => $choiceData) {
            $choice = isset($choiceData['id']) ? $question->choices()->find($choiceData['id']) : null;
            $translations = collect($choiceData['translations'] ?? [])->map(fn ($value) => ['text' => $value['text'] ?? null])->all();
            $translations = array_replace($choice?->translations ?? [], $translations);
            $data = [
                'translations' => $translations,
                'text_en' => data_get($translations, 'en.text') ?? '',
                'text_ku' => data_get($translations, 'ku.text'),
                'is_correct' => (int) $index === $request->integer('correct_choice'),
            ];
            if ($request->hasFile("choices.$index.image")) {
                $this->deleteFile($choice?->getRawOriginal('image_path'));
                $data['image_path'] = '/storage/'.$request->file("choices.$index.image")->store('questions/answers', 'public');
            } elseif ($request->input('question_type') !== 'image_answers') {
                $this->deleteFile($choice?->getRawOriginal('image_path'));
                $data['image_path'] = null;
            }
            $choice ? $choice->update($data) : $question->choices()->create($data);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $path), '/'));
    }
}
