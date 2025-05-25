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
        Schema::create('product_attribute_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'dpi', 'weight', 'num_keys'
            $table->string('display_name'); // e.g., 'DPI', 'Weight (grams)', 'Number of Keys'
            $table->enum('data_type', ['string', 'integer', 'decimal', 'boolean'])->default('string');
            $table->string('unit')->nullable(); // e.g., 'DPI', 'grams', 'keys'
            $table->boolean('is_filterable')->default(true); // Controls if this attribute appears in search filters
            $table->integer('sort_order')->default(0); // For ordering attributes in UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_keys');
    }
};
