@php
    $isEditing = isset($contentPage) && $contentPage;
    $currentType = old('type', $isEditing ? $contentPage->type : ($selectedType ?? 'cgi_clips'));
    $currentTopicId = old('topic_id', $isEditing ? $contentPage->topic_id : ($selectedTopicId ?? null));
    $hazardWindows = old('hazard_windows');

    if ($hazardWindows === null) {
        $hazardWindows = $isEditing ? $contentPage->hazard_windows : null;
    }

    if (! $hazardWindows && $isEditing && $contentPage->hazard_window_start !== null && $contentPage->hazard_window_end !== null) {
        $hazardWindows = [[
            'start' => $contentPage->hazard_window_start,
            'end' => $contentPage->hazard_window_end,
            'points' => 5,
        ]];
    }

    $hazardWindows = collect($hazardWindows ?: [['start' => '', 'end' => '', 'points' => 5, 'flag_time' => '']])
        ->values()
        ->map(fn ($window, $index) => [
            'key' => $index + 1,
            'start' => $window['start'] ?? '',
            'end' => $window['end'] ?? '',
            'points' => $window['points'] ?? 5,
            'flag_time' => $window['flag_time'] ?? '',
        ])
        ->all();
@endphp

<div x-data="{ type: @js($currentType), hazardWindows: @js($hazardWindows), nextHazardRangeId: {{ count($hazardWindows) + 1 }} }" class="space-y-6">
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold">Please correct the following:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-card rounded-lg border bg-white p-8">
        <div class="mb-6">
            <label class="mb-1 block text-sm font-medium text-gray-700">Page title *</label>
            <input name="admin_title" type="text" maxlength="150" required value="{{ old('admin_title', $isEditing ? $contentPage->admin_title : '') }}" placeholder="Example: No waiting except for loading" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
            <p class="mt-1 text-xs text-gray-500">Used only in the admin panel to help you find this learning page.</p>
            @error('admin_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Topic *</label>
                <select name="topic_id" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                    <option value="">Select a topic</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" @selected((string) $currentTopicId === (string) $topic->id)>{{ $topic->name_en }} ({{ class_basename($topic->topicable_type) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Page type *</label>
                <select name="type" x-model="type" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                    <option value="cgi_clips">CGI clips</option>
                    <option value="motorway_sign">Motorway sign</option>
                </select>
            </div>
        </div>
    </div>

    <section x-cloak x-show="type === 'cgi_clips'" class="admin-card rounded-lg border bg-white p-8">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900">CGI clips</h3>
            <p class="mt-1 text-sm text-gray-500">Upload the hazard video first, followed by the video explaining the hazard.</p>
        </div>

        @error('clips')
            <p class="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">{{ $message }}</p>
        @enderror

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            @for($slot = 0; $slot < 2; $slot++)
                @php($currentClip = $isEditing ? $contentPage->clips->firstWhere('slot', $slot) : null)
                <div class="admin-subcard rounded-lg border p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="font-bold text-gray-800">{{ $slot === 0 ? '1. Hazard video' : '2. Explanation video' }}</h4>
                        @if($currentClip)
                            <a href="{{ $currentClip->source }}" target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-primary hover:underline">View current clip</a>
                        @else
                            <span class="text-xs text-gray-400">Optional</span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Upload video *</label>
                            <input type="file" name="clips[{{ $slot }}][media]" accept="video/mp4,video/webm,video/ogg,video/quicktime" data-max-bytes="1073741824" :required="type === 'cgi_clips' && {{ $currentClip ? 'false' : 'true' }}" class="w-full rounded-md border border-gray-300 bg-white p-2 text-sm">
                            <p class="mt-1 text-xs text-gray-500">MP4, WebM, OGG or MOV; maximum 1 GB. {{ $currentClip ? 'Leave empty to keep the current video.' : '' }}</p>
                            @error("clips.$slot.media")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        @if($currentClip)
                            <label class="flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="clips[{{ $slot }}][remove]" value="1" @checked(old("clips.$slot.remove")) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                Remove the current clip
                            </label>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

        <div class="mt-7 rounded-lg border border-amber-200 bg-amber-50 p-5">
            <h4 class="font-bold text-amber-900">Hazard scoring windows</h4>
            <p class="mt-1 text-sm text-amber-800">Add one range for every developing hazard, then choose the maximum points available for that range.</p>

            @error('hazard_windows')<p class="mt-3 text-sm text-red-600">{{ $message }}</p>@enderror

            <div class="mt-4 space-y-4">
                <template x-for="(window, index) in hazardWindows" :key="window.key">
                    <div class="rounded-lg border border-amber-200 bg-white/80 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h5 class="font-bold text-gray-800">Hazard range <span x-text="index + 1"></span></h5>
                            <button x-show="hazardWindows.length > 1" type="button" @click="hazardWindows.splice(index, 1)" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Starts at (s) *</label>
                                <input type="number" :name="`hazard_windows[${index}][start]`" x-model="window.start" min="0" step="0.01" :required="type === 'cgi_clips'" placeholder="e.g. 8.50" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Ends at (s) *</label>
                                <input type="number" :name="`hazard_windows[${index}][end]`" x-model="window.end" min="0.01" step="0.01" :required="type === 'cgi_clips'" placeholder="e.g. 13.50" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Max points *</label>
                                <input type="number" :name="`hazard_windows[${index}][points]`" x-model="window.points" min="0" max="100" step="1" :required="type === 'cgi_clips'" placeholder="e.g. 5" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Flag time (s)</label>
                                <input type="number" :name="`hazard_windows[${index}][flag_time]`" x-model="window.flag_time" min="0" step="0.01" placeholder="defaults to start" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="hazardWindows.push({ key: nextHazardRangeId++, start: '', end: '', points: 5, flag_time: '' })" class="mt-4 inline-flex min-h-11 items-center gap-2 rounded-md border border-amber-400 bg-white px-4 py-2 text-sm font-bold text-amber-900 hover:bg-amber-100">
                <span class="text-lg leading-none">+</span> Add another hazard range
            </button>
        </div>

        <div class="mt-7 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Explanation text (English)</label>
                <textarea name="text_en" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('text_en', $isEditing ? $contentPage->text_en : '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Optional.</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Explanation text (Kurdish)</label>
                <textarea name="text_ku" dir="rtl" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('text_ku', $isEditing ? $contentPage->text_ku : '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Optional.</p>
            </div>
        </div>
    </section>

    <section x-cloak x-show="type === 'motorway_sign'" class="admin-card rounded-lg border bg-white p-8">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900">Motorway sign</h3>
            <p class="mt-1 text-sm text-gray-500">Build the complete sign guide shown to learners: the main sign, guidance, and related signs.</p>
        </div>

        <div class="space-y-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Sign image *</label>
                @if($isEditing && $contentPage->sign_image_path)
                    <div class="mb-3 inline-flex rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <img src="{{ $contentPage->sign_image_path }}" alt="Current motorway sign" class="h-32 w-48 object-contain">
                    </div>
                @endif
                <input type="file" name="sign_image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-md border border-gray-300 p-2 text-sm">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP; maximum 10 MB. {{ $isEditing && $contentPage->sign_image_path ? 'Leave empty to keep the current image.' : '' }}</p>
                @error('sign_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">About this sign (English) *</label>
                        <textarea name="explanation_en" rows="5" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('explanation_en', $isEditing ? $contentPage->explanation_en : '') }}</textarea>
                        @error('explanation_en')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">About this sign (Kurdish)</label>
                        <textarea name="explanation_ku" dir="rtl" rows="5" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('explanation_ku', $isEditing ? $contentPage->explanation_ku : '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">What to do (English) *</label>
                    <textarea name="what_to_do_en" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('what_to_do_en', $isEditing ? $contentPage->what_to_do_en : '') }}</textarea>
                    @error('what_to_do_en')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">What to do (Kurdish)</label>
                    <textarea name="what_to_do_ku" dir="rtl" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('what_to_do_ku', $isEditing ? $contentPage->what_to_do_ku : '') }}</textarea>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                <h4 class="font-bold text-gray-900">Additional signs learners can expect</h4>
                <p class="mt-1 text-sm text-gray-500">Add up to 8 related signs. They appear as small images below the guidance.</p>

                @if($isEditing && count($contentPage->additional_sign_images ?? []))
                    <div class="mt-4 flex flex-wrap gap-4">
                        @foreach($contentPage->additional_sign_images as $image)
                            <label class="relative rounded-lg border border-gray-200 bg-white p-3 text-center">
                                <img src="{{ $image }}" alt="Additional sign" class="h-20 w-24 object-contain">
                                <span class="mt-2 flex items-center justify-center gap-2 text-xs font-medium text-red-600">
                                    <input type="checkbox" name="remove_additional_sign_images[]" value="{{ $image }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Remove
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <input type="file" name="additional_sign_images[]" accept="image/jpeg,image/png,image/webp" multiple class="mt-4 w-full rounded-md border border-gray-300 p-2 text-sm">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP; maximum 5 MB per image.</p>
                @error('additional_sign_images')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @error('additional_sign_images.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.content-pages.index') }}" class="rounded-md border border-gray-300 bg-white px-5 py-2.5 font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="rounded-md bg-primary px-5 py-2.5 font-medium text-white hover:bg-primary-dark">{{ $isEditing ? 'Save Changes' : 'Create Learning Page' }}</button>
    </div>

    <div data-upload-progress class="hidden rounded-lg border border-blue-200 bg-blue-50 p-4" aria-live="polite">
        <div class="mb-2 flex items-center justify-between text-sm font-medium text-blue-900">
            <span data-upload-status>Uploading CGI clip…</span>
            <span data-upload-percent>0%</span>
        </div>
        <div class="h-2.5 w-full overflow-hidden rounded-full bg-blue-100">
            <div data-upload-bar class="h-full rounded-full bg-primary transition-[width] duration-150" style="width: 0%"></div>
        </div>
        <p class="mt-2 text-xs text-blue-800">Please keep this page open until the upload finishes.</p>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[action*="content-pages"]');
            if (!form || form.dataset.uploadProgressReady) return;
            form.dataset.uploadProgressReady = 'true';

            const progress = form.querySelector('[data-upload-progress]');
            const bar = form.querySelector('[data-upload-bar]');
            const percent = form.querySelector('[data-upload-percent]');
            const status = form.querySelector('[data-upload-status]');
            const submit = form.querySelector('button[type="submit"]');

            form.addEventListener('submit', (event) => {
                const files = [...form.querySelectorAll('input[type="file"]')]
                    .map((input) => input.files[0])
                    .filter(Boolean);

                const tooLarge = files.find((file) => file.size > 1073741824);
                if (tooLarge) {
                    event.preventDefault();
                    window.alert(`${tooLarge.name} is larger than the 1 GB limit.`);
                    return;
                }

                // Let the browser submit normally when this is a URL-only update.
                if (!files.length) return;

                event.preventDefault();
                progress.classList.remove('hidden');
                submit.disabled = true;
                submit.classList.add('cursor-not-allowed', 'opacity-60');

                const request = new XMLHttpRequest();
                request.open(form.method || 'POST', form.action, true);
                request.upload.addEventListener('progress', (uploadEvent) => {
                    if (!uploadEvent.lengthComputable) return;
                    const value = Math.round((uploadEvent.loaded / uploadEvent.total) * 100);
                    bar.style.width = `${value}%`;
                    percent.textContent = `${value}%`;
                });
                request.addEventListener('load', () => {
                    if (request.status >= 200 && request.status < 400) {
                        status.textContent = 'Upload complete. Saving CGI clip…';
                        bar.style.width = '100%';
                        percent.textContent = '100%';
                        window.location.href = request.responseURL || form.action;
                        return;
                    }

                    status.textContent = 'Upload failed. Please try again.';
                    submit.disabled = false;
                    submit.classList.remove('cursor-not-allowed', 'opacity-60');
                });
                request.addEventListener('error', () => {
                    status.textContent = 'Upload failed. Check your connection and try again.';
                    submit.disabled = false;
                    submit.classList.remove('cursor-not-allowed', 'opacity-60');
                });
                request.send(new FormData(form));
            });
        });
    </script>
@endonce
