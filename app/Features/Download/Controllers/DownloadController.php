<?php

declare(strict_types=1);

namespace App\Features\Download\Controllers;

use App\Enums\BookFileFormat;
use App\Enums\BookFileStatus;
use App\Features\Download\Services\DownloadService;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DownloadController extends Controller
{
    public function __construct(private readonly DownloadService $downloadService) {}

    public function show(Request $request, Book $book): RedirectResponse
    {
        Gate::authorize('download', $book);

        // Resolve requested format, defaulting to epub.
        $formatValue = $request->query('format', 'epub');

        // Controller gate (Rule 2): DOCX is never delivered to clients.
        $format = BookFileFormat::tryFrom((string) $formatValue);

        if ($format === null) {
            abort(422);
        }

        if (! $format->isClientAccessible()) {
            abort(403);
        }

        // Find a ready BookFile for the requested format.
        $bookFile = $book->files()
            ->where('format', $format)
            ->where('status', BookFileStatus::Ready)
            ->first();

        if ($bookFile === null) {
            abort(404);
        }

        /** @var User $user */
        $user = $request->user();

        $url = $this->downloadService->generateUrl($bookFile);

        $this->downloadService->logDownload($user, $bookFile, (string) $request->ip());

        return redirect($url);
    }
}
