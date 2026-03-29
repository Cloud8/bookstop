@extends('layouts.app')

@section('content')
<div>
    <h1>Панель управления</h1>
    <p>Всего книг: {{ $stats['total_books'] }}</p>
    <p>Опубликованных: {{ $stats['published_books'] }}</p>
    <p>Черновиков: {{ $stats['draft_books'] }}</p>
    <a href="{{ route('admin.books.index') }}">Книги</a>
</div>
@endsection
