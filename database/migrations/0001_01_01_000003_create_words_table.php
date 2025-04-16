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
        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->integer('word_id');
            $table->string('word', 100);
            $table->string('type', 20);
            $table->text('definition');
            $table->text('example');
            $table->string('us_ipa', 100);
            $table->string('uk_ipa', 100);
            $table->string('us_audio', 128);
            $table->string('uk_audio', 128);
            $table->string('image', 128);
            $table->text('synonyms');
            $table->text('antonyms');
            $table->text('family');
            $table->text('phrases');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'word_id'], 'user_word_unique');
        });

        Schema::create('flashcard_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->integer('set_id');
            $table->string('name', 100);
            $table->string('description', 256);
            $table->text('cards');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'set_id'], 'user_set_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('words');
        Schema::dropIfExists('flashcard_sets');
    }
};
