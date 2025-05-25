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
        // First, create the product_images table
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->nullable();
            $table->timestamps();
        });

        // Then, remove the image column from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, re-add the image column to products
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->after('description'); // Assuming description is the column before where image was
        });

        // Then, drop the product_images table
        Schema::dropIfExists('product_images');
    }
};
