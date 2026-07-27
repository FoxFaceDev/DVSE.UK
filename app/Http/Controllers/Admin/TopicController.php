<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('topicable')->get();
        return view('admin.topics.index', compact('topics'));
    }

    public function create(Request $request)
    {
        $topicableType = $request->query('topicable_type');
        $topicableId = $request->query('topicable_id');
        return view('admin.topics.create', compact('topicableType', 'topicableId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topicable_type' => 'required|string',
            'topicable_id' => 'required|integer',
            'name_en' => 'required|string|max:255',
            'name_ku' => 'nullable|string|max:255',
        ]);

        Topic::create($validated);
        
        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }

    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ku' => 'nullable|string|max:255',
        ]);

        $topic->update($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return redirect()->back()->with('success', 'Topic deleted successfully.');
    }
}
