<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    public function index() { return view('admin.languages.index', ['languages' => Language::orderBy('sort_order')->get()]); }
    public function create() { return view('admin.languages.form', ['language' => new Language]); }
    public function store(Request $request) { Language::create($this->validated($request)); return redirect()->route('admin.languages.index')->with('success', 'Language created.'); }
    public function edit(Language $language) { return view('admin.languages.form', compact('language')); }
    public function update(Request $request, Language $language) { $language->update($this->validated($request, $language)); return redirect()->route('admin.languages.index')->with('success', 'Language updated.'); }
    public function destroy(Language $language) { abort_if($language->code === 'en', 422, 'English cannot be deleted.'); $language->delete(); return back()->with('success', 'Language deleted. Existing translations are preserved.'); }

    private function validated(Request $request, ?Language $language = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'code' => ['required', 'alpha_dash:ascii', 'max:12', Rule::unique('languages')->ignore($language)],
            'direction' => ['required', Rule::in(['ltr', 'rtl'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['code'] = strtolower($data['code']);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
