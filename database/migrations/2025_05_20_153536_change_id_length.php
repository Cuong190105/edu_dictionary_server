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
        Schema::table('words', function (Blueprint $table) {
            $table->string('word_id', 100)->change();
            $table->string('us_audio', 100)->nullable()->change();
            $table->string('uk_audio', 100)->nullable()->change();
            $table->string('image', 100)->nullable()->change();
        });
        Schema::table('flashcard_sets', function (Blueprint $table) {
            $table->string('set_id', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
