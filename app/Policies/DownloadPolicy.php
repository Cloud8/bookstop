<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use App\Models\UserBook;

class DownloadPolicy
{
    public function download(User $user, Book $book): bool
    {
        return UserBook::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->exists();
    }
}
