<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('campaign_id')->index();
            $table->uuid('campaign_application_id')->unique()->index();

            $table->uuid('brand_user_id')->index();
            $table->uuid('influencer_user_id')->index();

            $table->string('status', 20)->default('open')->index();
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_reason', 120)->nullable();

            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
            $table->foreign('campaign_application_id')->references('id')->on('campaign_applications')->cascadeOnDelete();

            $table->foreign('brand_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('influencer_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};