<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 10)->default('MAD');
            $table->string('landing_url', 2048)->nullable();
            $table->string('status')->default('active');
            $table->json('images')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};