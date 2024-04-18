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
        Schema::table('s3files', function (Blueprint $table) {
            $table->dropColumn('xref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // do nothing
    }
};
