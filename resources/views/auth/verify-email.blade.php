@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl text-text-primary mb-2">Подтверждение email</h1>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl shadow-sm p-8">

            <p class="text-sm text-text-muted mb-6 leading-relaxed">
                Спасибо за регистрацию! Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке, которую мы отправили на указанный email. Если письмо не пришло, мы можем отправить его повторно.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    Новая ссылка для подтверждения отправлена на ваш email.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full px-4 py-2.5 bg-brand-700 hover:bg-brand-900 text-white font-sans text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Отправить повторно
                </button>
            </form>

        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
            @csrf
            <button type="submit" class="text-sm font-sans text-text-muted hover:text-text-primary transition">
                Выйти из аккаунта
            </button>
        </form>

    </div>
</div>

@endsection
