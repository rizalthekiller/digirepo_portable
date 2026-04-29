<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\ThesisFile;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thesis_files', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable()->unique();
        });

        // Populate existing records
        $files = ThesisFile::all();
        foreach ($files as $file) {
            if (empty($file->uuid)) {
                $file->uuid = Str::uuid()->toString();
                $file->save();
            }
        }
    }

    public function down(): void
    {
        Schema::table('thesis_files', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
