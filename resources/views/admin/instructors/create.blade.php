<x-layouts.admin title="Add Instructor">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('admin.instructors.index') }}" class="text-sm font-semibold text-primary hover:underline">← Back to instructors</a>
            <h2 class="mt-3 text-xl font-bold text-slate-900">Create instructor</h2>
            <p class="mt-1 text-sm text-slate-500">Create the login account and public directory profile together.</p>
        </div>
        <form method="POST" action="{{ route('admin.instructors.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.instructors._form', ['submitLabel' => 'Create Instructor'])
        </form>
    </div>
</x-layouts.admin>
