<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('target_year');
            $table->date('target_date')->nullable();
            $table->decimal('target_amount', 10, 2)->default(0.00);
            $table->decimal('current_amount', 10, 2)->default(0.00);
            $table->enum('status', ['in_progress', 'completed', 'cancelled', 'paused'])->default('in_progress');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};