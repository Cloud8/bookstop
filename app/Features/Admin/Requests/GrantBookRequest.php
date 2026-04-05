<?php

declare(strict_types=1);

namespace App\Features\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrantBookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
