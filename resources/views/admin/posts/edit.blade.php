@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6">Редактировать статью</h1>

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        {{-- Form fields will be built in Phase 11.5 --}}
        <button type="submit">Сохранить</button>
    </form>
</div>
@endsection
