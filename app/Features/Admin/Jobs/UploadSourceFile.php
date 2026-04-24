<?php

declare(strict_types=1);

namespace App\Features\Admin\Jobs;

use App\Enums\BookFileStatus;
use App\Features\Admin\Services\BookConversionService;
use App\Models\BookFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class UploadSourceFile implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * @param  int  $bookFileId  ID of the BookFile record (is_source=true, status=pending)
     * @param  string  $tempPath  Relative local storage  path to the temp file
     */
    public function __construct(
        public readonly int $bookFileId,
        public readonly string $tempPath,
    ) {
        $this->onQueue('default');
    }

    public function handle(BookConversionService $conversionService): void
    {
        $bookFile = BookFile::find($this->bookFileId);

        if (! $bookFile instanceof BookFile) {
            return;
        }

        $ext = $bookFile->format->extension();
        $s3Path = "books/{$bookFile->book_id}/".'source.'.$ext;

        $handle = Storage::disk('local')->readStream($this->tempPath);

        if (! is_resource($handle)) {
            throw new \RuntimeException("Cannot open temp file stream for reading: {$this->tempPath}");
        }

        try {
            if (Storage::disk('s3-private')->writeStream($s3Path, $handle)) {
                Storage::disk('local')->delete($this->tempPath);
                $bookFile->update([
                    'path' => $s3Path,
                    'status' => BookFileStatus::Ready,
                ]);

                $conversionService->dispatchConversions($bookFile);
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Called by Laravel after all retry attempts are exhausted.
     * Marks the BookFile as failed and cleans up the temp file.
     */
    public function failed(\Throwable $e): void
    {
        Storage::disk('local')->delete($this->tempPath);

        $bookFile = BookFile::find($this->bookFileId);
        $bookFile?->update([
            'status' => BookFileStatus::Failed,
            'error_message' => mb_substr($e->getMessage(), 0, 2000),
        ]);
    }
}
