<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAdminTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_non_admin_cannot_access_orders_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin/orders')->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_admin_can_view_orders_list(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/orders')->assertStatus(200);
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->pending()->create();
        Order::factory()->paid()->create();

        $response = $this->actingAs($admin)->get('/admin/orders?status=paid');

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function test_admin_can_view_order_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin)->get("/admin/orders/{$order->id}")->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // Refund
    // -------------------------------------------------------------------------

    public function test_admin_can_refund_an_order_and_revokes_user_books(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $order = Order::factory()->paid()->for($user)->create();

        $userBook = UserBook::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'order_id' => $order->id,
        ]);

        $this->assertNull($userBook->revoked_at);

        $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/refund")
            ->assertRedirect();

        $this->assertEquals(OrderStatus::Refunded, $order->fresh()->status);
        $this->assertNotNull($userBook->fresh()->revoked_at);
    }

    public function test_refund_revokes_all_user_books_in_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $order = Order::factory()->paid()->for($user)->create();

        $userBook1 = UserBook::factory()->create([
            'user_id' => $user->id,
            'book_id' => Book::factory()->create()->id,
            'order_id' => $order->id,
        ]);

        $userBook2 = UserBook::factory()->create([
            'user_id' => $user->id,
            'book_id' => Book::factory()->create()->id,
            'order_id' => $order->id,
        ]);

        $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/refund");

        $this->assertNotNull($userBook1->fresh()->revoked_at);
        $this->assertNotNull($userBook2->fresh()->revoked_at);
    }

    public function test_admin_cannot_refund_a_non_paid_order(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/refund");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_non_admin_gets_404_on_orders_routes(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($user)->get('/admin/orders')->assertStatus(404);
        $this->actingAs($user)->get("/admin/orders/{$order->id}")->assertStatus(404);
        $this->actingAs($user)->patch("/admin/orders/{$order->id}/refund")->assertStatus(404);
    }
}
