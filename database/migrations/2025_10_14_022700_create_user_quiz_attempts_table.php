<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // ✅  Ganti 'quiz_id' menjadi 'test_id' agar konsisten
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->enum('type', ['pre-test', 'post-test']);
            $table->unsignedInteger('score')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quiz_attempts');
    }
};
