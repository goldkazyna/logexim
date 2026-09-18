<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Прямые направления между городами без склада. Хранится в обе стороны
 * (city_id ↔ linked_city_id), чтобы связь была симметричной и её просто искать.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('city_links')) {
            Schema::create('city_links', function (Blueprint $table) {
                $table->unsignedBigInteger('city_id');
                $table->unsignedBigInteger('linked_city_id');
                $table->primary(['city_id', 'linked_city_id']);
                $table->index('linked_city_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('city_links');
    }
};
