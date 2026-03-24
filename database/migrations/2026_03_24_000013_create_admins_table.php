<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Дефолтный админ
        DB::table('admins')->insert([
            'login' => 'admin',
            'password' => sha1(md5('7774333822')),
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
