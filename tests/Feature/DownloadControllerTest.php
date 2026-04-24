<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookFileFormat;
use App\Enums\BookFileStatus;
use App\Models\Book;
use App\Models\BookFile;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class DownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    private function downloadUrl(Book $book, ?string $format = null): string
    {
        $url = route('books.download', $book);

        return $format !== null ? $url.'?format='.$format : $url;
    }

    /**
     * Swap the s3-private-presign disk with a mock that returns a predictable temporaryUrl.
     */
    private function mockPrivateDisk(): void
    {
        $mock = Mockery::mock(Filesystem::class);
        $mock->shouldReceive('temporaryUrl')
            ->andReturn('https://s3.example.com/fake-signed-url');

        Storage::set('s3-private-presign', $mock);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $book = Book::factory()->create();

        $response = $this->get($this->downloadUrl($book));

        $response->assertRedirectToRoute('login');
    }

    public function test_non_owner_gets_403(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        // No UserBook record — user does not own the book

        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertForbidden();
    }

    public function test_owner_can_download_epub(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->epub()->ready()->create(['book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'epub'));

        $response->assertRedirect('https://s3.example.com/fake-signed-url');
    }

    public function test_owner_can_download_fb2(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->fb2()->ready()->create(['book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'fb2'));

        $response->assertRedirect('https://s3.example.com/fake-signed-url');
    }

    public function test_default_format_is_epub(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->epub()->ready()->create(['book_id' => $book->id]);

        // No ?format= query param — should default to epub
        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertRedirect('https://s3.example.com/fake-signed-url');
    }

    public function test_docx_format_returns_403(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->docx()->ready()->create(['book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'docx'));

        $response->assertForbidden();
    }

    public function test_invalid_format_returns_422(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'xyz'));

        $response->assertStatus(422);
    }

    public function test_missing_book_file_returns_404(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        // No BookFile for epub — nothing in book_files at all

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'epub'));

        $response->assertNotFound();
    }

    public function test_non_ready_book_file_returns_404(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        // BookFile exists but status is pending (not ready)
        BookFile::factory()->epub()->create([
            'book_id' => $book->id,
            'status' => BookFileStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book, 'epub'));

        $response->assertNotFound();
    }

    public function test_book_without_ready_file_returns_404(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        $response = $this->actingAs($user)->get($this->downloadUrl($book));

        $response->assertNotFound();
    }

    public function test_download_log_records_format_correctly_for_epub(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->epub()->ready()->create(['book_id' => $book->id]);

        $this->actingAs($user)->get($this->downloadUrl($book, 'epub'));

        $this->assertDatabaseHas('download_logs', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'format' => BookFileFormat::Epub->value,
        ]);
    }

    public function test_download_log_records_format_correctly_for_fb2(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);
        BookFile::factory()->fb2()->ready()->create(['book_id' => $book->id]);

        $this->actingAs($user)->get($this->downloadUrl($book, 'fb2'));

        $this->assertDatabaseHas('download_logs', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'format' => BookFileFormat::Fb2->value,
        ]);
    }

    public function test_rate_limit_returns_429_after_10_requests(): void
    {
        $this->mockPrivateDisk();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        UserBook::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        // Clear any cached rate limiter state
        RateLimiter::clear('download:'.$user->id.':'.$book->id);

        // Without a ready BookFile, all requests return 404 — the throttle is still applied
        // at the route level and fires after 10 attempts regardless of response status.
        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)->get($this->downloadUrl($book));
        }

        $response = $this->actingAs($user)->get($this->downloadUrl($book));
        $response->assertStatus(429);
    }
}
