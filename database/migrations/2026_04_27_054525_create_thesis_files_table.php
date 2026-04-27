<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('thesis_id');
            $table->string('label'); // Contoh: "Cover", "Bab 1", "Full Text"
            $table->string('file_path');
            $table->boolean('is_public')->default(true); // Untuk kontrol akses per file
            $table->integer('order')->default(0); // Untuk urutan tampilan
            $table->timestamps();

            $table->foreign('thesis_id')->references('id')->on('theses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_files');
    }
};
