<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 120)->unique();
            $table->string('kata_sandi_hash'); // bcrypt/argon2
            $table->foreignId('peran_id')->constrained('peran')->cascadeOnUpdate()->restrictOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('peran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
