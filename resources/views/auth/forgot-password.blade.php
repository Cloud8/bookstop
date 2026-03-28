@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl text-text-primary mb-2">Восстановление пароля</h1>
            <p class="text-sm text-text-muted max-w-sm mx-auto">
                Укажите ваш email и мы отправим ссылку для создания нового пароля.
            </p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl shadow-sm p-8">

            {{-- Session status --}}
            @if (session('status'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-sans font-medium text-text-primary mb-1.5">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg border text-sm font-sans text-text-primary bg-surface placeholder:text-text-subtle transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                            @error('email') border-red-400 bg-red-50 @else border-border-subtle @enderror"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full px-4 py-2.5 bg-brand-700 hover:bg-brand-900 text-white font-sans text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Отправить ссылку
                </button>

            </form>
        </div>

        <p class="mt-6 text-center text-sm text-text-muted font-sans">
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-800 transition font-medium">
                Вернуться ко входу
            </a>
        </p>

    </div>
</div>

@endsection
