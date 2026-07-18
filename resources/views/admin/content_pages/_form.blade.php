@php
    $isEditing = isset($contentPage) && $contentPage;
    $currentType = old('type', $isEditing ? $contentPage->type : ($selectedType ?? 'cgi_clips'));
    $currentCategoryId = old('category_id', $isEditing ? $contentPage->category_id : ($selectedCategoryId ?? null));
@endphp

<div x-data="{ type: @js($currentType) }" class="space-y-6">
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
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Category *</label>
                <select name="category_id" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $currentCategoryId === (string) $category->id)>{{ $category->name_en }}</option>
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
            <p class="mt-1 text-sm text-gray-500">Add one to four clips. Each slot can use an uploaded video/GIF or an external URL.</p>
        </div>

        @error('clips')
            <p class="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">{{ $message }}</p>
        @enderror

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            @for($slot = 0; $slot < 4; $slot++)
                @php($currentClip = $isEditing ? $contentPage->clips->firstWhere('slot', $slot) : null)
                <div class="admin-subcard rounded-lg border p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="font-bold text-gray-800">Clip {{ $slot + 1 }}</h4>
                        @if($currentClip)
                            <a href="{{ $currentClip->source }}" target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-primary hover:underline">View current clip</a>
                        @else
                            <span class="text-xs text-gray-400">Optional</span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Upload a clip</label>
                            <input type="file" name="clips[{{ $slot }}][media]" accept="video/mp4,video/webm,video/ogg,video/quicktime,image/gif" class="w-full rounded-md border border-gray-300 bg-white p-2 text-sm">
                            @error("clips.$slot.media")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Or replace with a URL</label>
                            <input type="url" name="clips[{{ $slot }}][media_url]" value="{{ old("clips.$slot.media_url") }}" placeholder="https://..." class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-primary focus:ring-primary">
                            @error("clips.$slot.media_url")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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

        <div class="mt-7 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Text under clips (English)</label>
                <textarea name="text_en" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('text_en', $isEditing ? $contentPage->text_en : '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Optional.</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Text under clips (Kurdish)</label>
                <textarea name="text_ku" dir="rtl" rows="4" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('text_ku', $isEditing ? $contentPage->text_ku : '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Optional.</p>
            </div>
        </div>
    </section>

    <section x-cloak x-show="type === 'motorway_sign'" class="admin-card rounded-lg border bg-white p-8">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900">Motorway sign</h3>
            <p class="mt-1 text-sm text-gray-500">The mobile page shows the sign first and reveals the explanation when the learner taps the button.</p>
        </div>

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
                    <label class="mb-1 block text-sm font-medium text-gray-700">Explanation (English) *</label>
                    <textarea name="explanation_en" rows="5" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('explanation_en', $isEditing ? $contentPage->explanation_en : '') }}</textarea>
                    @error('explanation_en')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Explanation (Kurdish)</label>
                    <textarea name="explanation_ku" dir="rtl" rows="5" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary">{{ old('explanation_ku', $isEditing ? $contentPage->explanation_ku : '') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.content-pages.index') }}" class="rounded-md border border-gray-300 bg-white px-5 py-2.5 font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="rounded-md bg-primary px-5 py-2.5 font-medium text-white hover:bg-primary-dark">{{ $isEditing ? 'Save Changes' : 'Create Learning Page' }}</button>
    </div>
</div>
