<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('header');
            $table->string('alias');
            $table->string('mt');
            $table->string('md');
            $table->text('text');
            $table->integer('top_menu')->default(0);
            $table->integer('bottom_menu')->default(0);
            $table->integer('module');
            $table->integer('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_pages');
    }
};
