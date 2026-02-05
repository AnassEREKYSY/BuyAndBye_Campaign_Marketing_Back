<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('email')->unique();
            $table->string('password');

            $table->string('display_name')->nullable();
            $table->string('photo_url')->nullable();

            $table->string('role')->default('buyer');
            $table->string('status')->default('incomplete');

            $table->boolean('profile_completed')->default(false);
            $table->boolean('profile_skipped')->default(false);

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
