<?php

declare(strict_types=1);

namespace App\Features\Admin\Requests;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StorePostRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')],
            'excerpt' => ['required', 'string'],
            'body' => ['required', 'string'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', new Enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Введите заголовок статьи.',
            'title.max' => 'Заголовок не должен превышать 255 символов.',
            'slug.required' => 'Введите URL-адрес статьи.',
            'slug.max' => 'URL-адрес не должен превышать 255 символов.',
            'slug.unique' => 'Статья с таким URL-адресом уже существует.',
            'excerpt.required' => 'Введите краткое описание статьи.',
            'body.required' => 'Введите текст статьи.',
            'cover.image' => 'Обложка должна быть изображением.',
            'cover.max' => 'Размер обложки не должен превышать 5 МБ.',
            'status.required' => 'Выберите статус статьи.',
            'published_at.date' => 'Укажите корректную дату публикации.',
        ];
    }
}
