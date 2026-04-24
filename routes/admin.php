<?php

declare(strict_types=1);

use App\Features\Admin\Controllers\BookController as AdminBookController;
use App\Features\Admin\Controllers\BookFileController as AdminBookFileController;
use App\Features\Admin\Controllers\DashboardController;
use App\Features\Admin\Controllers\DownloadLogController as AdminDownloadLogController;
use App\Features\Admin\Controllers\NewsletterController as AdminNewsletterController;
use App\Features\Admin\Controllers\OrderController as AdminOrderController;
use App\Features\Admin\Controllers\PostController as AdminPostController;
use App\Features\Admin\Controllers\UserBookController as AdminUserBookController;
use App\Features\Admin\Controllers\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');
    Route::patch('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('books.toggle-status');
    Route::patch('/books/{book}/toggle-featured', [AdminBookController::class, 'toggleFeatured'])->name('books.toggle-featured');
    Route::patch('/books/{book}/toggle-availability', [AdminBookController::class, 'toggleAvailability'])->name('books.toggle-availability');

    // Book file management — status route defined first to avoid being captured as {file}
    Route::get('/books/{book}/files/status', [AdminBookFileController::class, 'status'])->name('books.files.status');
    Route::post('/books/{book}/files', [AdminBookFileController::class, 'store'])->name('books.files.store');
    Route::get('/books/{book}/files/{file}/download', [AdminBookFileController::class, 'download'])->name('books.files.download');
    Route::post('/books/{book}/files/{file}/retry', [AdminBookFileController::class, 'retry'])->name('books.files.retry');

    // Posts
    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/posts/{post}/toggle-status', [AdminPostController::class, 'toggleStatus'])->name('posts.toggle-status');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::patch('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
    Route::post('/users/{user}/send-password-reset', [AdminUserController::class, 'sendPasswordReset'])->name('users.send-password-reset');
    Route::post('/users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::post('/users/{user}/grant-book', [AdminUserBookController::class, 'grant'])->name('users.grant-book');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');

    // UserBooks
    Route::patch('/user-books/{userBook}/revoke', [AdminUserBookController::class, 'revoke'])->name('user-books.revoke');
    Route::patch('/user-books/{userBook}/restore', [AdminUserBookController::class, 'restore'])->name('user-books.restore');

    // Download logs
    Route::get('/download-logs', [AdminDownloadLogController::class, 'index'])->name('download-logs.index');

    // Newsletter
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::post('/newsletter/send', [AdminNewsletterController::class, 'send'])->name('newsletter.send');
});
