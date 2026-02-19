<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('influencer_profiles', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('niche')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->unsignedBigInteger('followers_instagram')->nullable();
            $table->unsignedBigInteger('followers_tiktok')->nullable();
            $table->unsignedBigInteger('followers_youtube')->nullable();
            $table->decimal('avg_engagement_rate', 5, 2)->nullable();
            $table->string('country_code')->nullable();
            $table->string('language')->nullable();
            $table->string('media_kit_url')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('influencer_profiles');
    }
};