<x-layouts.admin title="Create Advertisement">
    <div class="admin-card max-w-4xl rounded-lg border bg-white p-8">
        <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <h4 class="font-bold text-gray-900 border-b pb-2">Ad Details</h4>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Summer Driving School Promo" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <p class="text-xs text-gray-500 mt-1">Internal label only — not shown to users.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link URL *</label>
                <input type="url" name="link_url" required value="{{ old('link_url') }}" placeholder="https://advertiser-website.com" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <p class="text-xs text-gray-500 mt-1">Where users go when they click the ad.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Language *</label>
                <select name="language_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">Select the language this ad is made for</option>
                    @foreach($languages as $language)
                        <option value="{{ $language->id }}" @selected((string) old('language_id') === (string) $language->id)>
                            {{ $language->name }}{{ $language->is_active ? '' : ' (Inactive)' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">The ad is shown only when the learner selects this language.</p>
            </div>

            @include('admin.ads._category_targets', ['ad' => null])

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <label class="flex items-center gap-3 mt-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') === '1')
                        class="w-5 h-5 text-success rounded border-gray-300 focus:ring-success cursor-pointer">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <!-- Media Section -->
            <div x-data="{ mediaType: '{{ old('media_type', 'image') }}', mediaSource: 'upload' }" class="space-y-4">
                <h4 class="font-bold text-gray-900 border-b pb-2">Ad Media</h4>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Media Type *</label>
                    <select name="media_type" x-model="mediaType" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="mediaSource" value="upload" class="text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">Upload File</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="mediaSource" value="url" class="text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">External URL</span>
                    </label>
                </div>

                <div x-show="mediaSource === 'upload'" x-transition>
                    <input type="file" name="media" 
                        :accept="mediaType === 'video' ? 'video/mp4,video/webm,video/ogg' : 'image/*'"
                        class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Max file size: 100MB</p>
                </div>

                <div x-show="mediaSource === 'url'" x-transition>
                    <input type="url" name="media_url" placeholder="https://www.youtube.com/watch?v=..." 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Enter a direct link or a YouTube video URL.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-md border border-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-200">
                <a href="{{ route('admin.ads.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark shadow-sm transition">Create Advertisement</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
