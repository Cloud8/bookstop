@php
    $gaId = config('services.google_analytics_id');
@endphp

<div
    x-data="{
        visible: false,
        init() {
            if (!localStorage.getItem('cookie_consent')) {
                this.visible = true;
            } else {
                this.loadGA();
            }
        },
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.visible = false;
            this.loadGA();
        },
        loadGA() {
            @if($gaId)
            if (typeof gtag !== 'undefined') return;

            const gaId = @js($gaId);

            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=' + gaId;
            document.head.appendChild(script);

            script.onload = function() {
                window.dataLayer = window.dataLayer || [];
                function gtag(){ dataLayer.push(arguments); }
                window.gtag = gtag;
                gtag('js', new Date());
                gtag('config', gaId);
            };
            @endif
        }
    }"
    x-show="visible"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-0 left-0 right-0 z-50 p-4 sm:p-6"
    role="region"
    aria-label="Уведомление об использовании куки"
>
    <div class="mx-auto max-w-3xl bg-surface border border-border-subtle rounded-lg shadow-lg px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
        <p class="flex-1 text-sm text-text-muted leading-relaxed">
            Мы используем cookies для аналитики и улучшения работы сайта.
            Продолжая использовать сайт, вы соглашаетесь с их использованием.
        </p>
        <div class="flex items-center gap-3 shrink-0">
            <button
                type="button"
                @click="accept()"
                class="px-4 py-2 text-sm font-semibold text-white bg-brand-700 hover:bg-brand-800 rounded-md transition-colors"
            >
                Принять
            </button>
        </div>
    </div>
</div>
