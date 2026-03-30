<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'book_id'], 'cart_items_user_id_book_id_unique');
            $table->unique(['session_id', 'book_id'], 'cart_items_session_id_book_id_unique');
            $table->index('session_id', 'cart_items_session_id_index');
            $table->index('book_id', 'cart_items_book_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
