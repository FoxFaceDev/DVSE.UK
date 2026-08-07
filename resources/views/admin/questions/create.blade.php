<x-layouts.admin title="Add New Question">
    <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">@csrf
        @include('admin.questions._form', ['question' => null])
    </form>
</x-layouts.admin>
