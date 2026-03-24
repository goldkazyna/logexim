<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_news', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('title');
            $table->string('mt');
            $table->string('md');
            $table->text('discription');
            $table->text('text');
            $table->string('img');
            $table->string('thumb_571_315');
            $table->integer('visible');
            $table->string('title_eng');
            $table->text('discription_eng');
            $table->text('text_eng');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_news');
    }
};
