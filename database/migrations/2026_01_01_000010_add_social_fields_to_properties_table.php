<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('social_tiktok')->nullable()->after('video_url');
            $table->string('social_instagram')->nullable()->after('social_tiktok');
            $table->string('social_facebook')->nullable()->after('social_instagram');
            $table->string('social_youtube')->nullable()->after('social_facebook');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['social_tiktok', 'social_instagram', 'social_facebook', 'social_youtube']);
        });
    }
};