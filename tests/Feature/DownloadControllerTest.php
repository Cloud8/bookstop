<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeBookWithEpub(): Book
    {
        return Book::factory()->create(['epub_path' => 'epubs/test-book.epub']);
    }

    private function downloadUrl(Book $book): string
    {
        return route('books.download', $book);
    }

    public function test_owner_can_download_book(): void
    {
        Storage::fake('s3-private');

        $user = User::factory()->create();
        $book = $this->makeBookWithEpub();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        // Store a fake file so temporaryUrl does not fail
        Storage::disk('s3-private')->put('epubs/test-book.epub', 'epub content');

        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertRedirect();

        $this->assertDatabaseHas('download_logs', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $book = $this->makeBookWithEpub();

        $response = $this->get($this->downloadUrl($book));

        $response->assertRedirectToRoute('login');
    }

    public function test_non_owner_gets_403(): void
    {
        Storage::fake('s3-private');

        $user = User::factory()->create();
        $book = $this->makeBookWithEpub();
        // No UserBook record — user does not own the book

        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertForbidden();
    }

    public function test_book_without_epub_path_returns_404(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['epub_path' => null]);
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertNotFound();
    }

    public function test_rate_limit_returns_429_after_10_requests(): void
    {
        Storage::fake('s3-private');

        $user = User::factory()->create();
        $book = $this->makeBookWithEpub();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        Storage::disk('s3-private')->put('epubs/test-book.epub', 'epub content');

        // Clear any cached rate limiter state
        RateLimiter::clear('download:'.$user->id.':'.$book->id);

        // Hit the endpoint 10 times — all should pass
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($user)->get($this->downloadUrl($book));
            $response->assertRedirect();
        }

        // 11th request should be throttled
        $response = $this->actingAs($user)->get($this->downloadUrl($book));
        $response->assertStatus(429);
    }
}
