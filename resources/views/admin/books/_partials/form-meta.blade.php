{{-- Basic info: title, slug, price, status --}}
<div class="p-6 space-y-5">
    <h2 class="text-xs font-sans font-semibold text-text-muted uppercase tracking-widest">Основное</h2>

    <div>
        <label for="title" class="block text-sm font-medium text-text-primary mb-1.5">
            Название <span class="text-error">*</span>
        </label>
        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title', $book->title) }}"
            @input="onTitleInput($event)"
            required
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface placeholder:text-text-subtle transition
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('title') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >
        @error('title')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-text-primary mb-1.5">
            Slug <span class="text-error">*</span>
        </label>
        <input
            id="slug"
            x-ref="slug"
            type="text"
            name="slug"
            value="{{ old('slug', $book->slug) }}"
            @input="slugManuallyEdited = true"
            pattern="[a-zA-Z0-9_\-]+"
            title="Только латинские буквы, цифры, дефисы и подчёркивания"
            required
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface font-mono placeholder:text-text-subtle transition
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('slug') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >
        <p class="mt-1 text-xs text-text-subtle">Используется в URL книги.</p>
        @error('slug')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="block text-sm font-medium text-text-primary mb-1.5">
            Цена ({{ config('shop.currency_symbol') }}) <span class="text-error">*</span>
        </label>
        <input
            id="price"
            type="number"
            name="price"
            value="{{ old('price', $book->price / 100) }}"
            min="0"
            step="0.01"
            required
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface placeholder:text-text-subtle transition
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('price') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >
        <p class="mt-1 text-xs text-text-subtle">
            Текущая цена: {{ number_format($book->price / 100, config('shop.currency_decimals'), config('shop.currency_decimal_sep'), ' ') }} {{ config('shop.currency_symbol') }}
        </p>
        @error('price')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-text-primary mb-1.5">
            Статус <span class="text-error">*</span>
        </label>
        <select
            id="status"
            name="status"
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface transition
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('status') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >
            <option value="draft" @selected(old('status', $book->status->value) === 'draft')>Черновик</option>
            <option value="published" @selected(old('status', $book->status->value) === 'published')>Опубликована</option>
        </select>
        @error('status')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>
</div>
