<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id'); // receiver
            $table->string('type', 120);

            $table->string('title', 255);
            $table->text('body')->nullable();

            $table->uuid('actor_id')->nullable(); // who triggered it (optional)
            $table->string('entity_type', 120)->nullable(); // Campaign / Application / Payout / Collaboration
            $table->uuid('entity_id')->nullable();

            $table->json('data')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['type']);
            $table->index(['entity_type', 'entity_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};