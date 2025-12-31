<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sopirs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // satu user satu sopir
            $table->string('nama');
            $table->string('no_sim')->nullable();
            $table->enum('status', ['tidak aktif', 'aktif', 'bekerja'])->default('tidak aktif');
            $table->timestamps();

            // FK ke users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sopirs');
    }
};
