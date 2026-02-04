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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('seller_id');

            $table->string('title');
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');

            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
