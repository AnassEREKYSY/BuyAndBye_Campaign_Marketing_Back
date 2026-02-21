<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('click_events', function (Blueprint $table) {
            $table->string('unique_key', 64)->nullable()->index();
            $table->index(['tracking_link_id', 'created_at']);
            $table->index(['campaign_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('click_events', function (Blueprint $table) {
            $table->dropIndex(['click_events_unique_key_index']);
            $table->dropIndex(['click_events_tracking_link_id_created_at_index']);
            $table->dropIndex(['click_events_campaign_id_created_at_index']);
            $table->dropColumn('unique_key');
        });
    }
};