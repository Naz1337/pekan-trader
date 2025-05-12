<?php

use App\Models\Seller;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Seller::class);
            $table->string('name');
            $table->string('description');
            $table->decimal('price',15, 2)->default(0);
            $table->integer('stock_quantity');
            $table->string('image_path');
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->boolean('is_published');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
