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
        Schema::table('theses', function (Blueprint $table) {
            // Journal / Article Metadata
            $table->string('journal_name')->nullable()->after('type');
            $table->string('volume')->nullable()->after('journal_name');
            $table->string('issue')->nullable()->after('volume');
            $table->string('pages')->nullable()->after('issue');
            $table->string('issn')->nullable()->after('pages');
            $table->string('doi')->nullable()->after('issn');

            // Book / E-Book Metadata
            $table->string('isbn')->nullable()->after('doi');
            $table->string('publisher')->nullable()->after('isbn');
            $table->string('edition')->nullable()->after('publisher');
        });
    }

    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['journal_name', 'volume', 'issue', 'pages', 'issn', 'doi', 'isbn', 'publisher', 'edition']);
        });
    }
};
