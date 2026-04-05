<?php

declare(strict_types=1);

namespace App\Features\Admin\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Support\Facades\Log;

class UserBookAdminService
{
    /**
     * Revoke a user's access to a book.
     * Rule 81: sets revoked_at = now().
     */
    public function revoke(UserBook $userBook): void
    {
        $userBook->revoked_at = now();
        $userBook->save();
    }

    /**
     * Restore a previously revoked user book access.
     */
    public function restore(UserBook $userBook): void
    {
        $userBook->revoked_at = null;
        $userBook->save();
    }

    /**
     * Manually grant a book to a user.
     * Rule 82: creates user_books with order_id=null and granted_at=now(). Logs reason if provided.
     */
    public function grant(User $user, Book $book, ?string $reason = null): UserBook
    {
        $existing = UserBook::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->exists();

        if ($existing) {
            throw new \InvalidArgumentException('Пользователь уже владеет этой книгой.');
        }

        $userBook = UserBook::query()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'order_id' => null,
            'granted_at' => now(),
        ]);

        if ($reason !== null) {
            Log::info('Admin manually granted book to user.', [
                'admin_action' => 'grant_book',
                'user_id' => $user->id,
                'book_id' => $book->id,
                'user_book_id' => $userBook->id,
                'reason' => $reason,
            ]);
        }

        return $userBook;
    }
}
