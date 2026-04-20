<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookFileFormat;
use App\Enums\BookFileStatus;
use Database\Factories\BookFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $book_id
 * @property BookFileFormat $format
 * @property BookFileStatus $status
 * @property string|null $path
 * @property bool $is_source
 * @property string|null $error_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Book $book
 */
class BookFile extends Model
{
    /** @use HasFactory<BookFileFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'format',
        'status',
        'path',
        'is_source',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'format' => BookFileFormat::class,
            'status' => BookFileStatus::class,
            'is_source' => 'boolean',
        ];
    }

    /** @return BelongsTo<Book, $this> */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isReady(): bool
    {
        return $this->status === BookFileStatus::Ready;
    }

    public function isSource(): bool
    {
        return $this->is_source;
    }

    public function isClientAccessible(): bool
    {
        return $this->format->isClientAccessible();
    }
}
