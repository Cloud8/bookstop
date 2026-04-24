{{-- Extra settings: featured, adult content, sort order --}}
<div class="p-6 space-y-5">
    <h2 class="text-xs font-sans font-semibold text-text-muted uppercase tracking-widest">Дополнительно</h2>

    <div class="flex items-center gap-3">
        <input
            id="is_featured"
            type="checkbox"
            name="is_featured"
            value="1"
            @checked(old('is_featured', $book->is_featured))
            class="w-4 h-4 rounded border-border-subtle text-brand-600 focus:ring-brand-500 cursor-pointer"
        >
        <label for="is_featured" class="text-sm text-text-primary cursor-pointer">
            В избранном
            <span class="text-xs text-text-subtle ml-1">— показывать на главной странице</span>
        </label>
        @error('is_featured')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <input
            id="is_adult"
            type="checkbox"
            name="is_adult"
            value="1"
            @checked(old('is_adult', $book->is_adult))
            class="w-4 h-4 rounded border-border-subtle text-brand-600 focus:ring-brand-500 cursor-pointer"
        >
        <label for="is_adult" class="text-sm text-text-primary cursor-pointer">
            Контент 18+
            <span class="text-xs text-text-subtle ml-1">— требует подтверждения возраста</span>
        </label>
        @error('is_adult')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sort_order" class="block text-sm font-medium text-text-primary mb-1.5">
            Порядок сортировки
        </label>
        <input
            id="sort_order"
            type="number"
            name="sort_order"
            value="{{ old('sort_order', $book->sort_order) }}"
            min="0"
            class="w-32 px-3.5 py-2.5 rounded-lg border text-sm text-text-primary bg-surface transition
                focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent
                @error('sort_order') border-error-dot bg-error-light @else border-border-subtle @enderror"
        >
        <p class="mt-1 text-xs text-text-subtle">Меньшее число — выше в списке.</p>
        @error('sort_order')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>
</div>
