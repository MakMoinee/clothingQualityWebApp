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
        Schema::create('detections', function (Blueprint $table) {
            $table->id('detectionID')->autoIncrement();
            $table->string('imagePath')->nullable(false);
            $table->string('status')->nullable(false);
            $table->boolean('defect')->nullable(false)->default(false);
            $table->string('remarks')->nullable(true);
            $table->integer('userID')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
