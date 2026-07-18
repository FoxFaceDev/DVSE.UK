<x-layouts.admin title="Edit Learning Page">
    <form action="{{ route('admin.content-pages.update', $contentPage) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.content_pages._form')
    </form>
</x-layouts.admin>
