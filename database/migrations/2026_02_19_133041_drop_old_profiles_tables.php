<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('seller_profiles');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('products');
    }

    public function down(): void
    {
    }
};