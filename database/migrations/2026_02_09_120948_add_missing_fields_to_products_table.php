<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('category_id')->nullable();
            $table->string('condition')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->json('images')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();

            $table->boolean('is_digital')->default(false);
            $table->boolean('allow_returns')->default(true);
            $table->integer('return_days')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'category_id',
                'condition',
                'stock_quantity',
                'images',
                'tags',
                'weight_kg',
                'is_digital',
                'allow_returns',
                'return_days',
                'is_featured',
                'published_at',
            ]);
        });
    }
};
