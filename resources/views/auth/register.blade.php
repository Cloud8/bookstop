@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl text-text-primary mb-2">Создать аккаунт</h1>
            <p class="text-sm text-text-muted">Присоединяйтесь к Книжной лавке</p>
        </div>

        <div class="bg-surface border border-border-subtle rounded-xl shadow-sm p-8">

            {{-- OAuth buttons --}}
            <div class="flex flex-col gap-3 mb-6">
                <a
                    href="{{ route('auth.oauth.redirect', 'google') }}"
                    class="flex items-center justify-center gap-3 w-full px-4 py-2.5 border border-border-subtle rounded-lg text-sm font-sans font-medium text-text-primary bg-surface hover:bg-surface-muted transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Войти через Google
                </a>
                <a
                    href="{{ route('auth.oauth.redirect', 'vk') }}"
                    class="flex items-center justify-center gap-3 w-full px-4 py-2.5 border border-border-subtle rounded-lg text-sm font-sans font-medium text-text-primary bg-surface hover:bg-surface-muted transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="#0077FF" aria-hidden="true">
                        <path d="M12.785 16.241s.288-.032.436-.194c.136-.148.132-.427.132-.427s-.02-1.304.585-1.496c.598-.19 1.365 1.26 2.179 1.815.615.42 1.082.328 1.082.328l2.175-.03s1.138-.071.598-1.047c-.044-.077-.315-.664-1.62-1.876-1.366-1.267-1.183-1.062.462-3.253.999-1.33 1.398-2.143 1.273-2.49-.12-.332-.854-.244-.854-.244l-2.447.015s-.182-.025-.316.056c-.132.079-.217.264-.217.264s-.386 1.03-.901 1.907c-1.085 1.847-1.52 1.946-1.698 1.831-.413-.267-.31-1.075-.31-1.648 0-1.793.271-2.54-.528-2.736-.265-.064-.46-.107-1.137-.114-.87-.009-1.606.003-2.023.207-.278.135-.492.437-.361.454.161.021.526.099.72.363.25.341.241 1.107.241 1.107s.144 2.11-.335 2.372c-.329.18-.78-.187-1.748-1.86-.496-.858-.872-1.808-.872-1.808s-.072-.178-.202-.274c-.158-.115-.378-.152-.378-.152l-2.325.015s-.349.01-.477.162C4.045 8.88 4.144 9.19 4.144 9.19s1.817 4.259 3.875 6.403c1.886 1.966 4.028 1.837 4.028 1.837h.738z"/>
                    </svg>
                    Войти через VK
                </a>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="flex-1 h-px bg-border-subtle"></div>
                <span class="text-xs text-text-subtle font-sans">или по email</span>
                <div class="flex-1 h-px bg-border-subtle"></div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-sans font-medium text-text-primary mb-1.5">
                        Имя
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full px-3.5 py-2.5 rounded-lg border text-sm font-sans text-text-primary bg-surface placeholder:text-text-subtle transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                            @error('name') border-red-400 bg-red-50 @else border-border-subtle @enderror"
                        placeholder="Как вас зовут?"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                        autocomplete="username"
                        class="w-full px-3.5 py-2.5 rounded-lg border text-sm font-sans text-text-primary bg-surface placeholder:text-text-subtle transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                            @error('email') border-red-400 bg-red-50 @else border-border-subtle @enderror"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-sans font-medium text-text-primary mb-1.5">
                        Пароль
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full px-3.5 py-2.5 rounded-lg border text-sm font-sans text-text-primary bg-surface transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                            @error('password') border-red-400 bg-red-50 @else border-border-subtle @enderror"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-sans font-medium text-text-primary mb-1.5">
                        Подтвердите пароль
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-border-subtle text-sm font-sans text-text-primary bg-surface transition
                            focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                    >
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Terms --}}
                <div class="space-y-3 pt-1">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="terms"
                            required
                            class="mt-0.5 rounded border-border-subtle text-brand-600 focus:ring-brand-500 shrink-0"
                        >
                        <span class="text-sm font-sans text-text-muted leading-snug">
                            Я принимаю условия пользования
                        </span>
                    </label>
                    @error('terms')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="newsletter_consent"
                            value="1"
                            class="mt-0.5 rounded border-border-subtle text-brand-600 focus:ring-brand-500 shrink-0"
                        >
                        <span class="text-sm font-sans text-text-muted leading-snug">
                            Подписаться на новости и акции
                        </span>
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full px-4 py-2.5 bg-brand-700 hover:bg-brand-900 text-white font-sans text-sm font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Зарегистрироваться
                </button>

            </form>
        </div>

        <p class="mt-6 text-center text-sm text-text-muted font-sans">
            Уже есть аккаунт?
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-800 transition font-medium">
                Войти
            </a>
        </p>

    </div>
</div>

@endsection
