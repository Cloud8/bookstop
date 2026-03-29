@extends('layouts.app')

@section('content')
<div>
    <h1>Создать книгу</h1>
    <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" value="{{ old('title') }}" placeholder="Название">
        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="Slug">
        <input type="number" name="price" value="{{ old('price') }}" placeholder="Цена (руб.)">
        <select name="status">
            <option value="draft">Черновик</option>
            <option value="published">Опубликована</option>
        </select>
        <textarea name="annotation">{{ old('annotation') }}</textarea>
        <textarea name="excerpt">{{ old('excerpt') }}</textarea>
        <textarea name="fragment">{{ old('fragment') }}</textarea>
        <input type="checkbox" name="is_featured" value="1"> В избранных
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}">
        <input type="file" name="cover">
        <input type="file" name="epub">
        <button type="submit">Создать</button>
    </form>
</div>
@endsection
