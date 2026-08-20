<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('operation', ['venta', 'alquiler', 'anticretico']);
            $table->enum('type', ['casa', 'departamento', 'penthouse', 'garzonier', 'condominio', 'terreno']);
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price', 14, 2)->default(0);
            $table->string('currency', 10)->default('USD');   // USD | BS
            $table->string('price_suffix')->nullable();        // ej: "/mes"
            $table->unsignedSmallInteger('bedrooms')->default(0);
            $table->unsignedSmallInteger('bathrooms')->default(0);
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->unsignedSmallInteger('parking')->default(0);
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->json('features')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('disponible'); // disponible|reservada|vendida
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('properties'); }
};