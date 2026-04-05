@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6">Статьи</h1>

    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary mb-4">Создать статью</a>

    @foreach ($posts as $post)
        <div class="border rounded p-4 mb-2">
            <span>{{ $post->title }}</span>
            <span class="text-sm text-gray-500">{{ $post->status->value }}</span>
            <a href="{{ route('admin.posts.edit', $post) }}">Редактировать</a>
        </div>
    @endforeach

    {{ $posts->links() }}
</div>
@endsection
