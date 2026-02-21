<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('collaboration_payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('collaboration_id')->index();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->unsignedBigInteger('clicks_total')->default(0);
            $table->unsignedBigInteger('clicks_unique')->default(0);
            $table->uuid('tier_id')->nullable()->index();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('MAD');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('collaboration_id')->references('id')->on('collaborations')->cascadeOnDelete();
            $table->foreign('tier_id')->references('id')->on('campaign_payout_tiers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaboration_payouts');
    }
};