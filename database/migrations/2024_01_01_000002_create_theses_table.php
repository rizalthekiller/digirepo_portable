<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('title');
            $table->string('year', 4);
            $table->text('abstract');
            $table->text('keywords')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->enum('type', ['Skripsi', 'Thesis', 'Disertasi'])->default('Skripsi');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('was_rejected')->default(false);
            $table->string('certificate_number')->nullable();
            $table->string('verification_hash')->nullable();
            $table->longText('certificate_content')->nullable();
            $table->enum('delivery_status', ['pending', 'sent', 'failed'])->default('pending');
            $table->boolean('is_watermarked')->default(false);
            $table->timestamps();
            
            // Add fulltext index for search
            $table->fullText(['title', 'abstract', 'keywords']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
