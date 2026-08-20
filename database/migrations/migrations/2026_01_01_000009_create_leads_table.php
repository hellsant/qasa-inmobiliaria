<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('contacto'); // contacto | tasacion
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('interest')->nullable();
            $table->string('zone')->nullable();
            $table->string('property_type')->nullable();
            $table->string('operation')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('leads'); }
};