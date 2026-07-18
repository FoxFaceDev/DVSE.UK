<x-layouts.admin title="Add Learning Page">
    <form action="{{ route('admin.content-pages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.content_pages._form', ['contentPage' => null])
    </form>
</x-layouts.admin>
