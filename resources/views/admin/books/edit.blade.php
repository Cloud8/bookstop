@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-10">

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="font-serif text-2xl text-text-primary">Редактировать книгу</h1>
        <p class="text-sm text-text-muted mt-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-700 transition">Панель управления</a>
            &rsaquo;
            <a href="{{ route('admin.books.index') }}" class="hover:text-brand-700 transition">Книги</a>
            &rsaquo; {{ $book->title }}
        </p>
    </div>

    {{-- Flash success --}}
    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-success-light border border-success-border rounded-lg text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-error-light border border-error-border rounded-lg text-sm text-error">
            <p class="font-medium mb-1">Пожалуйста, исправьте ошибки:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <p class="mt-2 text-xs opacity-75">Загруженные файлы (обложка) были сброшены браузером — прикрепите их повторно перед отправкой.</p>
        </div>
    @endif

    {{-- Main book form: metadata, text content, covers, extra settings --}}
    {{-- NOTE: The transliterate/slugify logic below is duplicated in create.blade.php.
         This is intentional until a shared Alpine.js module is introduced. --}}
    <form
        method="POST"
        action="{{ route('admin.books.update', $book) }}"
        enctype="multipart/form-data"
        x-data="{
            slugManuallyEdited: true,
            transliterate(text) {
                const map = {
                    'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo',
                    'ж':'zh','з':'z','и':'i','й':'j','к':'k','л':'l','м':'m',
                    'н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u',
                    'ф':'f','х':'h','ц':'ts','ч':'ch','ш':'sh','щ':'shch',
                    'ъ':'','ы':'y','ь':'','э':'e','ю':'yu','я':'ya',
                    'А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'Yo',
                    'Ж':'Zh','З':'Z','И':'I','Й':'J','К':'K','Л':'L','М':'M',
                    'Н':'N','О':'O','П':'P','Р':'R','С':'S','Т':'T','У':'U',
                    'Ф':'F','Х':'H','Ц':'Ts','Ч':'Ch','Ш':'Sh','Щ':'Shch',
                    'Ъ':'','Ы':'Y','Ь':'','Э':'E','Ю':'Yu','Я':'Ya'
                };
                return text.split('').map(c => map[c] !== undefined ? map[c] : c).join('');
            },
            slugify(text) {
                return this.transliterate(text)
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/[\s_]+/g, '-')
                    .replace(/-+/g, '-');
            },
            onTitleInput(event) {
                if (!this.slugManuallyEdited) {
                    this.$refs.slug.value = this.slugify(event.target.value);
                }
            }
        }"
    >
        @csrf
        @method('PUT')

        <div class="bg-surface border border-border-subtle rounded-xl divide-y divide-border-subtle">
            @include('admin.books._partials.form-meta')
            @include('admin.books._partials.form-content')
            @include('admin.books._partials.form-covers')
            @include('admin.books._partials.form-extra')
        </div>

        {{-- Form actions --}}
        <div class="flex items-center justify-between mt-6">
            <a
                href="{{ route('admin.books.index') }}"
                class="px-4 py-2.5 text-sm font-medium text-text-primary border border-border-subtle rounded-lg hover:bg-surface-muted transition"
            >
                Отмена
            </a>
            <button
                type="submit"
                class="px-6 py-2.5 bg-brand-700 hover:bg-brand-900 text-white text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
            >
                Сохранить изменения
            </button>
        </div>

    </form>

    {{-- Book files: separate section with its own action forms, outside the main form --}}
    @include('admin.books._partials.book-files')

    {{-- Delete section --}}
    <div
        class="mt-10 border border-error-border rounded-xl p-6"
        x-data="{ open: false }"
    >
        <h2 class="text-sm font-sans font-semibold text-text-primary mb-1">Удалить книгу</h2>
        <p class="text-sm text-text-muted mb-4">
            Книга «{{ $book->title }}» и все связанные файлы будут удалены без возможности восстановления.
        </p>
        <button
            @click="open = true"
            class="px-4 py-2 text-sm font-medium text-error border border-error-border rounded-lg hover:bg-error-light transition focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2"
        >
            Удалить книгу
        </button>

        {{-- Confirmation modal --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center px-4"
            style="display: none;"
        >
            <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
            <div class="relative bg-surface rounded-xl shadow-xl p-6 max-w-sm w-full z-10">
                <h3 class="font-serif text-lg text-text-primary mb-2">Удалить книгу?</h3>
                <p class="text-sm text-text-muted mb-6">
                    «{{ $book->title }}» будет удалена вместе с файлами. Это действие необратимо.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-text-primary border border-border-subtle rounded-lg hover:bg-surface-muted transition"
                    >
                        Отмена
                    </button>
                    <form method="POST" action="{{ route('admin.books.destroy', $book) }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-error-muted hover:bg-error-hover rounded-lg transition focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2"
                        >
                            Удалить
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
