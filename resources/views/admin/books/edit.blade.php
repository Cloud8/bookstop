@extends('layouts.app')

@section('content')
<div>
    <h1>Редактировать книгу: {{ $book->title }}</h1>
    <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="text" name="title" value="{{ old('title', $book->title) }}" placeholder="Название">
        <input type="text" name="slug" value="{{ old('slug', $book->slug) }}" placeholder="Slug">
        <input type="number" name="price" value="{{ old('price', $book->price / 100) }}" placeholder="Цена (руб.)">
        <select name="status">
            <option value="draft" @selected(old('status', $book->status->value) === 'draft')>Черновик</option>
            <option value="published" @selected(old('status', $book->status->value) === 'published')>Опубликована</option>
        </select>
        <textarea name="annotation">{{ old('annotation', $book->annotation) }}</textarea>
        <textarea name="excerpt">{{ old('excerpt', $book->excerpt) }}</textarea>
        <textarea name="fragment">{{ old('fragment', $book->fragment) }}</textarea>
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured))> В избранных
        <input type="number" name="sort_order" value="{{ old('sort_order', $book->sort_order) }}">
        <input type="file" name="cover">
        <input type="file" name="epub">
        <button type="submit">Сохранить</button>
    </form>
    <a href="{{ route('admin.books.index') }}">Назад</a>
</div>
@endsection
