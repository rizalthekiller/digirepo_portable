<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_types', function (Blueprint $row) {
            $row->id();
            $row->string('name')->unique();
            $row->string('slug')->unique();
            $row->text('description')->nullable();
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_types');
    }
};
