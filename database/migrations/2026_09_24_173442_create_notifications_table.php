<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // User who receives the notification
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Notification category/type
            $table->string('type');

            // Notification heading and message
            $table->string('title');
            $table->text('message');

            // Optional extra information
            $table->json('data')->nullable();

            // NULL = unread, timestamp = read
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Useful for fetching unread notifications quickly
            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};