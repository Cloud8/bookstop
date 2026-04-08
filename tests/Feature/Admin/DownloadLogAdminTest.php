<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Book;
use App\Models\DownloadLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadLogAdminTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_non_admin_cannot_access_download_logs(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/download-logs')->assertStatus(404);
    }

    public function test_guest_cannot_access_download_logs(): void
    {
        $this->get('/admin/download-logs')->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_admin_can_view_download_logs(): void
    {
        $admin = User::factory()->admin()->create();
        DownloadLog::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/download-logs')->assertStatus(200);
    }

    public function test_admin_can_filter_logs_by_user_id(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create();
        $otherUser = User::factory()->create();

        DownloadLog::factory()->create(['user_id' => $targetUser->id]);
        DownloadLog::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($admin)
            ->get("/admin/download-logs?user_id={$targetUser->id}");

        $response->assertStatus(200);

        // The page should include data relating to the target user's log
        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
        $this->assertEquals($targetUser->id, $logs->first()->user_id);
    }

    public function test_admin_can_filter_logs_by_book_id(): void
    {
        $admin = User::factory()->admin()->create();
        $targetBook = Book::factory()->create();
        $otherBook = Book::factory()->create();

        DownloadLog::factory()->create(['book_id' => $targetBook->id]);
        DownloadLog::factory()->create(['book_id' => $otherBook->id]);

        $response = $this->actingAs($admin)
            ->get("/admin/download-logs?book_id={$targetBook->id}");

        $response->assertStatus(200);

        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
        $this->assertEquals($targetBook->id, $logs->first()->book_id);
    }

    public function test_download_logs_index_without_filter_returns_all_logs(): void
    {
        $admin = User::factory()->admin()->create();
        DownloadLog::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/admin/download-logs');

        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        $this->assertCount(5, $logs);
    }
}
