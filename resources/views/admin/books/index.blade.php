@extends('layouts.app')

@section('content')
<div>
    <h1>Книги</h1>
    <a href="{{ route('admin.books.create') }}">Добавить книгу</a>
    @foreach($books as $book)
    <div>
        <span>{{ $book->title }}</span>
        <span>{{ $book->status->value }}</span>
        <a href="{{ route('admin.books.edit', $book) }}">Редактировать</a>
        <form method="POST" action="{{ route('admin.books.toggle-status', $book) }}">
            @csrf @method('PATCH')
            <button type="submit">Переключить статус</button>
        </form>
        <form method="POST" action="{{ route('admin.books.toggle-featured', $book) }}">
            @csrf @method('PATCH')
            <button type="submit">Переключить featured</button>
        </form>
        <form method="POST" action="{{ route('admin.books.destroy', $book) }}">
            @csrf @method('DELETE')
            <button type="submit">Удалить</button>
        </form>
    </div>
    @endforeach
</div>
@endsection
