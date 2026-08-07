@php
    $editing = isset($question) && $question;
    $questionTranslations = $editing ? ($question->translations ?? []) : [];
    $choiceRows = $editing ? $question->choices->values() : collect(array_fill(0, 4, null));
@endphp
<div x-data="{ tab: 'en', type: @js(old('question_type', $editing ? $question->question_type : 'text')) }" class="space-y-6">
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="rounded-lg border bg-white p-6 shadow-sm">
        <div class="grid gap-5 md:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium">Topic *</label><select name="topic_id" required class="w-full rounded-md border-gray-300"><option value="">Select a topic</option>@foreach($topics as $topic)<option value="{{ $topic->id }}" @selected((string) old('topic_id', $editing ? $question->topic_id : $selectedTopicId ?? '') === (string) $topic->id)>{{ $topic->name_en }}</option>@endforeach</select></div>
            <div><label class="mb-1 block text-sm font-medium">Answer type *</label><select name="question_type" x-model="type" class="w-full rounded-md border-gray-300"><option value="text">Four text answers</option><option value="image_answers">Four image answers</option></select></div>
        </div>
    </div>

    <div class="rounded-lg border bg-white shadow-sm">
        <div class="flex flex-wrap gap-1 border-b bg-slate-50 p-2">
            @foreach($languages as $language)<button type="button" @click="tab='{{ $language->code }}'" :class="tab==='{{ $language->code }}' ? 'bg-primary text-white' : 'bg-white text-gray-700'" class="rounded-md border px-4 py-2 text-sm font-bold">{{ $language->name }}</button>@endforeach
        </div>

        @foreach($languages as $language)
            @php
                $legacyText = $language->code === 'en' ? ($question->text_en ?? '') : ($language->code === 'ku' ? ($question->text_ku ?? '') : '');
                $legacyExplanation = $language->code === 'en' ? ($question->explanation_en ?? '') : ($language->code === 'ku' ? ($question->explanation_ku ?? '') : '');
            @endphp
            <section x-cloak x-show="tab==='{{ $language->code }}'" class="space-y-6 p-6" dir="{{ $language->direction }}">
                <div><label class="mb-1 block text-sm font-medium">Question in {{ $language->name }} {{ $language->code === 'en' ? '*' : '' }}</label><textarea name="translations[{{ $language->code }}][text]" rows="3" {{ $language->code === 'en' ? 'required' : '' }} class="w-full rounded-md border-gray-300">{{ old("translations.{$language->code}.text", data_get($questionTranslations, "{$language->code}.text", $legacyText)) }}</textarea></div>
                <div><label class="mb-1 block text-sm font-medium">Explanation in {{ $language->name }}</label><textarea name="translations[{{ $language->code }}][explanation]" rows="3" class="w-full rounded-md border-gray-300">{{ old("translations.{$language->code}.explanation", data_get($questionTranslations, "{$language->code}.explanation", $legacyExplanation)) }}</textarea></div>
                <div x-show="type==='text'" class="grid gap-4 md:grid-cols-2">
                    @foreach($choiceRows as $i => $choice)
                        @php($choiceTranslations = $choice?->translations ?? [])
                        <div class="rounded-lg border bg-gray-50 p-4"><label class="mb-1 block text-xs font-bold">Answer {{ $i + 1 }} in {{ $language->name }}</label><input type="text" name="choices[{{ $i }}][translations][{{ $language->code }}][text]" value="{{ old("choices.$i.translations.{$language->code}.text", data_get($choiceTranslations, "{$language->code}.text", $language->code === 'en' ? ($choice?->text_en ?? '') : ($language->code === 'ku' ? ($choice?->text_ku ?? '') : ''))) }}" :required="type==='text' && tab==='{{ $language->code }}' && {{ $language->code === 'en' ? 'true' : 'false' }}" class="w-full rounded-md border-gray-300"></div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="border-t p-6">
            <h3 class="mb-3 font-bold">Shared answer settings</h3>
            <p class="mb-4 text-sm text-gray-500">Choose the correct answer once. This selection and all media are shared by every language.</p>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($choiceRows as $i => $choice)
                    <div class="rounded-lg border p-4">
                        @if($choice)<input type="hidden" name="choices[{{ $i }}][id]" value="{{ $choice->id }}">@endif
                        <label class="flex items-center gap-2 font-bold"><input type="radio" name="correct_choice" value="{{ $i }}" required @checked((string) old('correct_choice', $choice?->is_correct ? $i : ($editing ? '' : 0)) === (string) $i)> Answer {{ $i + 1 }}</label>
                        <div x-show="type==='image_answers'" class="mt-3">@if($choice?->image_path)<img src="{{ $choice->image_path }}" class="mb-2 h-28 w-full rounded object-contain bg-gray-100"><input type="hidden" name="choices[{{ $i }}][existing_image]" value="1">@endif<input type="file" name="choices[{{ $i }}][image]" accept="image/*" class="w-full text-xs"><p class="mt-1 text-xs text-gray-500">Upload once for all languages.</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-lg border bg-white p-6"><h3 class="mb-4 font-bold">Shared question media</h3><div class="grid gap-4 md:grid-cols-3"><select name="media_type" class="rounded-md border-gray-300"><option value="">No media</option>@foreach(['image','video','gif'] as $typeOption)<option value="{{ $typeOption }}" @selected(old('media_type', $question->media_type ?? '') === $typeOption)>{{ ucfirst($typeOption) }}</option>@endforeach</select><input type="file" name="media" class="rounded-md border p-2"><input type="url" name="media_url" value="{{ old('media_url', $question->media_url ?? '') }}" placeholder="Or external media URL" class="rounded-md border-gray-300"></div>@if($editing && $question->media_source)<label class="mt-3 flex gap-2 text-sm text-red-600"><input type="checkbox" name="remove_media" value="1"> Remove current media</label>@endif</div>
    <div class="flex justify-end gap-3"><a href="{{ route('admin.questions.index') }}" class="rounded-md border bg-white px-5 py-2.5">Cancel</a><button class="rounded-md bg-primary px-5 py-2.5 font-bold text-white">Save Question</button></div>
</div>
