@php
    $selectedParentType = (string) old('topicable_type', $selectedParentType);
    $selectedParentId = (string) old('topicable_id', $selectedParentId);
@endphp

<div
    class="grid gap-4 sm:grid-cols-2"
    x-data="{
        parentType: @js($selectedParentType),
        parentId: @js($selectedParentId),
        parents: {{ Js::from($parentOptions) }},
        parentTypeChanged() {
            const availableParents = this.parents[this.parentType] || [];
            if (!availableParents.some(parent => String(parent.id) === String(this.parentId))) {
                this.parentId = '';
            }
        }
    }"
>
    <div>
        <label for="topicable_type" class="mb-1 block text-sm font-medium text-gray-700">Parent Type</label>
        <select id="topicable_type" name="topicable_type" x-model="parentType" @change="parentTypeChanged()" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-primary focus:ring-primary">
            <option value="App\Models\Section">Section</option>
            <option value="App\Models\SubSection">Sub-Section</option>
            <option value="App\Models\Category">Category</option>
        </select>
        @error('topicable_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="topicable_id" class="mb-1 block text-sm font-medium text-gray-700">Parent Name</label>
        <select id="topicable_id" name="topicable_id" x-model="parentId" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-primary focus:ring-primary">
            <option value="">Select a parent</option>
            <template x-for="parent in (parents[parentType] || [])" :key="parent.id">
                <option :value="String(parent.id)" x-text="parent.label"></option>
            </template>
        </select>
        @error('topicable_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
