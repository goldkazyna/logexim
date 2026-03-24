<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avia', function (Blueprint $table) {
            $table->id();
            $table->integer('city_from');
            $table->integer('city_to');
            $table->integer('price');
            $table->string('time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avia');
    }
};
