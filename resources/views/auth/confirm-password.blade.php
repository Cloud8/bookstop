@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl text-text-primary mb-2">Подтверждение пароля</h1>
            <p class="text-sm text-text-muted max-w-sm mx-auto">
                Это защищённый раздел. Пожалуйста, введите ваш пароль для продолжения.
            </p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl shadow-sm p-8">

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-sans font-medium text-text-primary mb-1.5">
                        Текущий пароль
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-3.5 py-2.5 rounded-lg border text-sm font-sans text-text-primary bg-surface transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                            @error('password') border-red-400 bg-red-50 @else border-border-subtle @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full px-4 py-2.5 bg-brand-700 hover:bg-brand-900 text-white font-sans text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Подтвердить
                </button>

            </form>
        </div>

    </div>
</div>

@endsection
