<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->uuid('user_id')->primary();

            $table->string('phone_number', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('locale', 10)->nullable();

            $table->json('buyer_categories')->nullable();
            $table->json('buyer_interests')->nullable();
            $table->json('payment_methods')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
