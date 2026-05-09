<x-layouts.admin title="Add New Question">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-900 border-b pb-2">Question Details</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <option value="">Select a category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $selectedCategoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question (English) *</label>
                        <textarea name="text_en" required rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question (Kurdish)</label>
                        <textarea name="text_ku" dir="rtl" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"></textarea>
                    </div>

                    <!-- Media Section -->
                    <div x-data="{ mediaType: '', mediaSource: 'upload' }" class="space-y-4">
                        <h4 class="font-bold text-gray-900 border-b pb-2">Media Attachment</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Media Type</label>
                            <select name="media_type" x-model="mediaType" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="">No Media</option>
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                                <option value="gif">GIF</option>
                            </select>
                        </div>

                        <template x-if="mediaType">
                            <div class="space-y-4">
                                <!-- Source toggle -->
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

                                <!-- File Upload -->
                                <div x-show="mediaSource === 'upload'" x-transition>
                                    <input type="file" name="media" 
                                        :accept="mediaType === 'video' ? 'video/mp4,video/webm,video/ogg' : (mediaType === 'gif' ? 'image/gif' : 'image/*')"
                                        class="w-full border border-gray-300 rounded-md p-2 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span x-show="mediaType === 'image'">Accepts: JPG, PNG, WebP. Max 100MB.</span>
                                        <span x-show="mediaType === 'video'">Accepts: MP4, WebM, OGG. Max 100MB.</span>
                                        <span x-show="mediaType === 'gif'">Accepts: GIF files. Max 100MB.</span>
                                    </p>
                                </div>

                                <!-- URL Input -->
                                <div x-show="mediaSource === 'url'" x-transition>
                                    <input type="url" name="media_url" placeholder="https://www.youtube.com/watch?v=..." 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Enter a direct link or a YouTube video URL.</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-4">
                        <h4 class="font-bold text-gray-900 border-b pb-2 mb-4">Explanation (Shown after answering)</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Explanation (English)</label>
                                <textarea name="explanation_en" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-opacity-50"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Explanation (Kurdish)</label>
                                <textarea name="explanation_ku" dir="rtl" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-opacity-50"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Choices -->
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-900 border-b pb-2 flex items-center justify-between">
                        <span>Answer Choices</span>
                        <span class="text-xs font-normal text-gray-500">Select the radio button for the correct answer</span>
                    </h4>
                    
                    @for ($i = 0; $i < 4; $i++)
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 relative {{ $i === 0 ? 'bg-green-50 border-green-200' : '' }}" x-data="{ isCorrect: {{ $i === 0 ? 'true' : 'false' }} }">
                        <div class="absolute -left-3 -top-3 w-6 h-6 bg-white border border-gray-300 rounded-full flex items-center justify-center font-bold text-xs text-gray-500 shadow-sm">{{ $i + 1 }}</div>
                        
                        <div class="flex items-start gap-4 mb-3">
                            <div class="pt-1">
                                <input type="radio" name="correct_choice" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} 
                                    class="w-5 h-5 text-success border-gray-300 focus:ring-success cursor-pointer"
                                    x-on:change="isCorrect = true"
                                    onClick="document.querySelectorAll('[x-data]').forEach(el => el.__x && (el.__x.$data.isCorrect = false)); isCorrect = true;">
                            </div>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <input type="text" name="choices[{{ $i }}][text_en]" required placeholder="English answer text..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                                </div>
                                <div>
                                    <input type="text" name="choices[{{ $i }}][text_ku]" dir="rtl" placeholder="Kurdish answer text..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-200">
                <a href="{{ route('admin.questions.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark shadow-sm transition">Save Question</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
