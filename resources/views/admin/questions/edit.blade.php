<x-layouts.admin title="Edit Question">
    <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        @include('admin.questions._form')
    </form>
</x-layouts.admin>
