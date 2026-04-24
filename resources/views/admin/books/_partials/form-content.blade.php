{{-- Text content: annotation, excerpt, fragment --}}
<div class="p-6 space-y-5">
    <h2 class="text-xs font-sans font-semibold text-text-muted uppercase tracking-widest">Текстовый контент</h2>

    <div>
        <label for="annotation" class="block text-sm font-medium text-text-primary mb-1.5">
            Аннотация
        </label>
        <textarea
            id="annotation"
            name="annotation"
            rows="4"
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface placeholder:text-text-subtle transition resize-y
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('annotation') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >{{ old('annotation', $book->annotation) }}</textarea>
        @error('annotation')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="excerpt" class="block text-sm font-medium text-text-primary mb-1.5">
            Отрывок
        </label>
        <textarea
            id="excerpt"
            name="excerpt"
            rows="6"
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface placeholder:text-text-subtle transition resize-y
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('excerpt') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >{{ old('excerpt', $book->excerpt) }}</textarea>
        @error('excerpt')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fragment" class="block text-sm font-medium text-text-primary mb-1.5">
            Фрагмент
            <span class="text-xs font-normal text-text-subtle ml-1">(для страницы ознакомительного чтения)</span>
        </label>
        <textarea
            id="fragment"
            name="fragment"
            rows="12"
            class="w-full px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface placeholder:text-text-subtle transition resize-y
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('fragment') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >{{ old('fragment', $book->fragment) }}</textarea>
        @error('fragment')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>
</div>
