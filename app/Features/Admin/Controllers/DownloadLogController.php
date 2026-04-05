<?php

declare(strict_types=1);

namespace App\Features\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DownloadLogController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->integer('user_id') ?: null;
        $bookId = $request->integer('book_id') ?: null;

        $logs = DownloadLog::query()
            ->with(['user', 'book'])
            ->when($userId !== null, fn ($q) => $q->where('user_id', $userId))
            ->when($bookId !== null, fn ($q) => $q->where('book_id', $bookId))
            ->orderByDesc('downloaded_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.download-logs.index', compact('logs', 'userId', 'bookId'));
    }
}
