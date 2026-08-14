<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MockTest;
use App\Models\SubSection;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MockTestController extends Controller
{
    public function index()
    {
        return view('admin.mock_tests.index', ['mockTests' => MockTest::with(['subSection.section', 'topics'])->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.mock_tests.form', ['mockTest' => new MockTest(['type' => 'theory', 'question_count' => 50, 'video_question_count' => 3, 'duration_minutes' => 57, 'pass_mark' => 43, 'is_active' => true]), 'subSections' => SubSection::with('section')->get(), 'topics' => Topic::orderBy('name_en')->get()]);
    }

    public function store(Request $request)
    {
        $mockTest = MockTest::create($this->validated($request));
        $mockTest->topics()->sync($request->input('topic_ids', []));

        return redirect()->route('admin.mock-tests.index')->with('success', 'Mock test created and placed successfully.');
    }

    public function edit(MockTest $mockTest)
    {
        $mockTest->load('topics');

        return view('admin.mock_tests.form', ['mockTest' => $mockTest, 'subSections' => SubSection::with('section')->get(), 'topics' => Topic::orderBy('name_en')->get()]);
    }

    public function update(Request $request, MockTest $mockTest)
    {
        $mockTest->update($this->validated($request));
        $mockTest->topics()->sync($request->input('topic_ids', []));

        return redirect()->route('admin.mock-tests.index')->with('success', 'Mock test updated.');
    }

    public function destroy(MockTest $mockTest)
    {
        $mockTest->delete();

        return back()->with('success', 'Mock test deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'sub_section_id' => ['required', 'exists:sub_sections,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['theory', 'hazard'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'question_count' => ['required_if:type,theory', 'integer', 'min:1', 'max:100'],
            'video_question_count' => ['required_if:type,theory', 'integer', 'min:0', 'max:10', 'lte:question_count'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:180'],
            'pass_mark' => ['required', 'integer', 'min:1', 'max:100'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['integer', 'distinct', 'exists:topics,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        if ($data['type'] === 'hazard') {
            $data['question_count'] = 14;
            $data['video_question_count'] = 0;
        }
        unset($data['topic_ids']);

        return $data;
    }
}
