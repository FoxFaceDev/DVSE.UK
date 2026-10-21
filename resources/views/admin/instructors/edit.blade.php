<x-layouts.admin title="Edit Instructor">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('admin.instructors.index') }}" class="text-sm font-semibold text-primary hover:underline">← Back to instructors</a>
                <h2 class="mt-3 text-xl font-bold text-slate-900">Edit {{ $instructor->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">Update the account, profile, and directory visibility.</p>
            </div>
            @if($instructor->instructorProfile?->is_published)
                <a href="{{ route('instructors.show', $instructor) }}" target="_blank" class="text-sm font-semibold text-primary hover:underline">View public profile ↗</a>
            @endif
        </div>

        @if(session('success'))<p class="mb-5 rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">{{ session('success') }}</p>@endif

        <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.instructors._form', ['submitLabel' => 'Save Changes'])
        </form>
    </div>
</x-layouts.admin>
