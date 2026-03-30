<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->restrictOnDelete();
            $table->unsignedInteger('price')->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->timestamps();

            $table->index('order_id', 'order_items_order_id_index');
            $table->index('book_id', 'order_items_book_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
