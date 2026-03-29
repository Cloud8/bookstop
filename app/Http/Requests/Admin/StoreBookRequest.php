<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\BookStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreBookRequest extends FormRequest
{
    /**
     * Admin middleware already enforces admin role — if we got here, we're authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('books', 'slug')],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', new Enum(BookStatus::class)],
            'cover' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'epub' => ['nullable', 'file', 'mimes:epub', 'max:102400'],
            'annotation' => ['nullable', 'string', 'max:5000'],
            'excerpt' => ['nullable', 'string', 'max:10000'],
            'fragment' => ['nullable', 'string', 'max:100000'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
