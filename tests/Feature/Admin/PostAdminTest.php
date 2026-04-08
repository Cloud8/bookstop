<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostAdminTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_non_admin_gets_404_on_posts_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/posts')->assertStatus(404);
    }

    public function test_guest_is_redirected_from_posts_index(): void
    {
        $this->get('/admin/posts')->assertRedirect('/login');
    }

    // -------------------------------------------------------------------------
    // List posts
    // -------------------------------------------------------------------------

    public function test_admin_can_list_posts(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/posts')->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Create post
    // -------------------------------------------------------------------------

    public function test_admin_can_create_a_post(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Тестовая статья',
            'slug' => 'test-post',
            'excerpt' => 'Краткое описание',
            'body' => '<p>Текст статьи</p>',
            'status' => PostStatus::Draft->value,
        ]);

        $post = Post::query()->where('slug', 'test-post')->firstOrFail();

        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertDatabaseHas('posts', [
            'slug' => 'test-post',
            'status' => 'draft',
        ]);
    }

    public function test_body_script_tags_are_stripped_on_create(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'XSS Test',
            'slug' => 'xss-test',
            'excerpt' => 'Test',
            'body' => '<p>Safe content</p><script>alert("xss")</script>',
            'status' => PostStatus::Draft->value,
        ]);

        $post = Post::query()->where('slug', 'xss-test')->firstOrFail();
        $this->assertStringNotContainsString('<script>', $post->body);
        $this->assertStringContainsString('Safe content', $post->body);
    }

    public function test_admin_can_create_a_post_with_cover(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Статья с обложкой',
            'slug' => 'post-with-cover',
            'excerpt' => 'Описание',
            'body' => '<p>Текст</p>',
            'cover' => UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg'),
            'status' => PostStatus::Draft->value,
        ]);

        $post = Post::query()->where('slug', 'post-with-cover')->firstOrFail();
        $this->assertNotNull($post->cover_path);
        Storage::disk('s3-public')->assertExists($post->cover_path);
    }

    public function test_create_post_validates_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/posts', []);

        $response->assertSessionHasErrors(['title', 'slug', 'excerpt', 'body', 'status']);
    }

    public function test_create_post_validates_unique_slug(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['slug' => 'existing-slug']);

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Дубль',
            'slug' => 'existing-slug',
            'excerpt' => 'Текст',
            'body' => '<p>Текст</p>',
            'status' => PostStatus::Draft->value,
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    // -------------------------------------------------------------------------
    // Update post
    // -------------------------------------------------------------------------

    public function test_admin_can_update_a_post(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['title' => 'Старый заголовок']);

        $response = $this->actingAs($admin)->put("/admin/posts/{$post->slug}", [
            'title' => 'Новый заголовок',
            'slug' => $post->slug,
            'excerpt' => 'Новое описание',
            'body' => '<p>Новый текст</p>',
            'status' => PostStatus::Draft->value,
        ]);

        $response->assertRedirect(route('admin.posts.edit', $post));
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Новый заголовок',
        ]);
    }

    public function test_body_script_tags_are_stripped_on_update(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin)->put("/admin/posts/{$post->slug}", [
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'body' => '<p>Чистый текст</p><script>evil()</script>',
            'status' => PostStatus::Draft->value,
        ]);

        $this->assertStringNotContainsString('<script>', $post->fresh()->body);
    }

    public function test_update_post_slug_unique_ignores_current_post(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['slug' => 'my-post']);

        $response = $this->actingAs($admin)->put("/admin/posts/{$post->slug}", [
            'title' => $post->title,
            'slug' => 'my-post',
            'excerpt' => $post->excerpt,
            'body' => '<p>Текст</p>',
            'status' => PostStatus::Draft->value,
        ]);

        $response->assertSessionMissing('errors');
        $response->assertRedirect(route('admin.posts.edit', $post));
    }

    // -------------------------------------------------------------------------
    // Delete post
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_a_post(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/posts/{$post->slug}");

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_deleting_post_removes_cover_from_storage(): void
    {
        Storage::fake('s3-public');

        $admin = User::factory()->admin()->create();

        Storage::disk('s3-public')->put('posts/covers/cover.jpg', 'image-content');
        $post = Post::factory()->create(['cover_path' => 'posts/covers/cover.jpg']);

        $this->actingAs($admin)->delete("/admin/posts/{$post->slug}");

        Storage::disk('s3-public')->assertMissing('posts/covers/cover.jpg');
    }

    // -------------------------------------------------------------------------
    // Toggle status
    // -------------------------------------------------------------------------

    public function test_admin_can_toggle_post_status_to_published(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['status' => PostStatus::Draft, 'published_at' => null]);

        $response = $this->actingAs($admin)->patch("/admin/posts/{$post->slug}/toggle-status");

        $response->assertOk();
        $response->assertJson(['status' => 'published']);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'published',
        ]);
        $this->assertNotNull($post->fresh()->published_at);
    }

    public function test_admin_can_toggle_post_status_to_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($admin)->patch("/admin/posts/{$post->slug}/toggle-status");

        $response->assertOk();
        $response->assertJson(['status' => 'draft']);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'draft',
        ]);
    }

    public function test_toggle_status_preserves_existing_published_at(): void
    {
        $admin = User::factory()->admin()->create();
        $existingDate = now()->subDays(10);
        $post = Post::factory()->create([
            'status' => PostStatus::Draft,
            'published_at' => $existingDate,
        ]);

        $this->actingAs($admin)->patch("/admin/posts/{$post->slug}/toggle-status");

        // published_at should not be overwritten if it was already set
        $this->assertEquals(
            $existingDate->timestamp,
            $post->fresh()->published_at->timestamp,
        );
    }
}
