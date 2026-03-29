<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Book;
use App\Services\BookFileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessBookFileUpload implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  string  $tempPath  Absolute path to the temp file on disk
     * @param  string  $originalExtension  Original file extension (e.g. 'epub')
     */
    public function __construct(
        public readonly int $bookId,
        public readonly string $tempPath,
        public readonly string $originalExtension,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(BookFileService $fileService): void
    {
        $book = Book::query()->find($this->bookId);

        if (! $book instanceof Book) {
            return;
        }

        if ($book->epub_path) {
            Storage::disk('s3-private')->delete($book->epub_path);
        }

        $epubPath = 'epubs/'.Str::uuid().'.epub';

        $content = file_get_contents($this->tempPath);

        if ($content !== false) {
            Storage::disk('s3-private')->put($epubPath, $content, 'private');
            $book->update(['epub_path' => $epubPath]);
        }

        if (file_exists($this->tempPath)) {
            unlink($this->tempPath);
        }
    }
}
