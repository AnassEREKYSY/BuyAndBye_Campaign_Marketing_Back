<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tracking_link_id')->index();
            $table->uuid('campaign_id')->index();
            $table->uuid('influencer_id')->index();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->timestamps();

            $table->foreign('tracking_link_id')->references('id')->on('tracking_links')->cascadeOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
            $table->foreign('influencer_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};