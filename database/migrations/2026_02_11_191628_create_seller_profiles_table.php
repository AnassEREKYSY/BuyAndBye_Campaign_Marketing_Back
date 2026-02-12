<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {

            $table->uuid('user_id')->primary();

            $table->string('store_name', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('vat_number', 100)->nullable();
            $table->string('support_email', 255)->nullable();
            $table->string('support_phone', 50)->nullable();

            $table->json('category_tags')->nullable();
            $table->text('store_description')->nullable();
            $table->longText('store_banner_url')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
