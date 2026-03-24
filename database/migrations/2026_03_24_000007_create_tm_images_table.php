<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_images', function (Blueprint $table) {
            $table->id();
            $table->string('img');
            $table->string('title');
            $table->date('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_images');
    }
};
