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
        Schema::table('flashcard_sets', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->default(null)->change();
            $table->timestamp('created_at')->nullable()->default(null)->change();
            $table->timestamp('created_at')->nullable()->default(null)->change();
        });
        Schema::table('words', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->default(null)->change();
            $table->timestamp('updated_at')->nullable()->default(null)->change();
            $table->timestamp('deleted_at')->nullable()->default(null)->change();
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
