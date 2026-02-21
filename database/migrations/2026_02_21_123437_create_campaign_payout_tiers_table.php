<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaign_payout_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('campaign_id')->index();
            $table->string('metric', 50)->default('clicks');
            $table->unsignedBigInteger('from_value');
            $table->unsignedBigInteger('to_value')->nullable();
            $table->decimal('payout_amount', 12, 2);
            $table->string('currency', 10)->default('MAD');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_payout_tiers');
    }
};