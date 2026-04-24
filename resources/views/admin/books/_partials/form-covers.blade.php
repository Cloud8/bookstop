{{-- Cover images: cover + cover_thumb --}}
<div class="p-6 space-y-5">
    <h2 class="text-xs font-sans font-semibold text-text-muted uppercase tracking-widest">Файлы</h2>

    <div>
        <label for="cover" class="block text-sm font-medium text-text-primary mb-1.5">
            Обложка
            <span class="text-xs font-normal text-text-subtle ml-1">(jpg, png, webp — до 5 МБ)</span>
        </label>
        @if ($book->cover_url)
            <div class="mb-3 flex items-start gap-4">
                <img
                    src="{{ $book->cover_url }}"
                    alt="Текущая обложка"
                    class="w-20 h-28 object-cover rounded-lg border border-border-subtle"
                >
                <div class="text-xs text-text-muted pt-1">
                    <p class="font-medium text-text-primary mb-0.5">Текущая обложка</p>
                    <p>Загрузите новый файл, чтобы заменить.</p>
                </div>
            </div>
        @endif
        <input
            id="cover"
            type="file"
            name="cover"
            accept="image/jpeg,image/png,image/webp"
            class="w-full text-sm text-text-muted
                file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-border-subtle
                file:text-sm file:font-medium file:text-text-primary file:bg-surface-muted
                hover:file:bg-brand-50 hover:file:border-brand-300 hover:file:text-brand-700
                file:transition file:cursor-pointer cursor-pointer"
        >
        @error('cover')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="cover_thumb" class="block text-sm font-medium text-text-primary mb-1.5">
            Миниатюра обложки
            <span class="text-xs font-normal text-text-subtle ml-1">(jpg, png, webp — до 2 МБ)</span>
        </label>
        @if ($book->cover_thumb_url)
            <div class="mb-3 flex items-start gap-4">
                <img
                    src="{{ $book->cover_thumb_url }}"
                    alt="Текущая миниатюра"
                    class="w-10 h-14 object-cover rounded border border-border-subtle"
                >
                <div class="text-xs text-text-muted pt-1">
                    <p class="font-medium text-text-primary mb-0.5">Текущая миниатюра</p>
                    <p>Загрузите новый файл, чтобы заменить.</p>
                </div>
            </div>
        @endif
        <input
            id="cover_thumb"
            type="file"
            name="cover_thumb"
            accept="image/jpeg,image/png,image/webp"
            class="w-full text-sm text-text-muted
                file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-border-subtle
                file:text-sm file:font-medium file:text-text-primary file:bg-surface-muted
                hover:file:bg-brand-50 hover:file:border-brand-300 hover:file:text-brand-700
                file:transition file:cursor-pointer cursor-pointer"
        >
        @error('cover_thumb')
            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
        @enderror
    </div>
</div>
