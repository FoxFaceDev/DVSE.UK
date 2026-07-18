<x-layouts.admin title="Edit Advertisement">
    <div class="admin-card max-w-4xl rounded-lg border bg-white p-8">
        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <h4 class="font-bold text-gray-900 border-b pb-2">Ad Details</h4>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $ad->title) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link URL *</label>
                <input type="url" name="link_url" required value="{{ old('link_url', $ad->link_url) }}" 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            @include('admin.ads._category_targets')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <label class="flex items-center gap-3 mt-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $ad->is_active ? '1' : '0') === '1')
                        class="w-5 h-5 text-success rounded border-gray-300 focus:ring-success cursor-pointer">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <!-- Media Section -->
            <div x-data="{ mediaType: '{{ old('media_type', $ad->media_type) }}', mediaSource: '{{ $ad->getRawOriginal('media_path') ? 'upload' : ($ad->media_url ? 'url' : 'upload') }}' }" class="space-y-4">
                <h4 class="font-bold text-gray-900 border-b pb-2">Ad Media</h4>

                <!-- Current Media Preview -->
                @if($ad->media_source)
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Current Media ({{ ucfirst($ad->media_type) }}):</p>
                        @if($ad->media_type === 'video')
                            @if($ad->is_youtube)
                                <div class="aspect-video mb-3">
                                    <iframe class="w-full h-full rounded border border-white shadow-sm" src="https://www.youtube.com/embed/{{ $ad->youtube_id }}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @else
                                <video src="{{ $ad->media_source }}" controls playsinline preload="metadata" class="max-h-40 rounded border border-white shadow-sm mb-3 w-full"></video>
                            @endif
                        @else
                            <img src="{{ $ad->media_source }}" alt="Ad Media" class="max-h-40 rounded border border-white shadow-sm mb-3">
                        @endif
                        <label class="flex items-center gap-2 text-xs text-red-600 font-medium cursor-pointer hover:text-red-700">
                            <input type="checkbox" name="remove_media" value="1" class="rounded text-red-600 focus:ring-red-500">
                            Remove current media
                        </label>
                    </div>
                @endif

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
                        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white">
                    <p class="text-xs text-gray-500 mt-1">Uploading a new file will replace the existing one. Max 100MB.</p>
                </div>

                <div x-show="mediaSource === 'url'" x-transition>
                    <input type="url" name="media_url" value="{{ old('media_url', $ad->media_url) }}" placeholder="https://example.com/ad-banner.jpg" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Full URL to the media file.</p>
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
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark shadow-sm transition">Update Advertisement</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
