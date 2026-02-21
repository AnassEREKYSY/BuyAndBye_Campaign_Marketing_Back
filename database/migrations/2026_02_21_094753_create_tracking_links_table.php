<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tracking_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('collaboration_id')->index();
            $table->string('code', 80)->unique();
            $table->string('destination_url', 2048);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('collaboration_id')->references('id')->on('collaborations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_links');
    }
};