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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert the column to not nullable if needed,
            // but be aware of potential data loss if there are null values.
            // For a simple rollback, you might just remove the nullable constraint.
            // However, if you had non-nullable data before, this might fail.
            // A more robust down() might involve checking for nulls and handling them.
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
