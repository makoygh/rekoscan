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
        Schema::create('s3files', function (Blueprint $table) {
            $table->id();
            $table->string('img_name');
            $table->string('img_filename');
            $table->string('img_localfile')->nullable();
            $table->longText('img_analysis')->nullable();
            $table->longtext('img_chatgpt_title')->nullable();
            $table->longtext('img_chatgpt_content')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s3files');
    }
};
