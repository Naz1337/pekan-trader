<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();

            $table->string('business_name');
            $table->string('business_description');
            $table->string('business_address');
            $table->string('business_phone');
            $table->string('business_email');
            $table->string('logo_url');
            $table->string('opening_hour');
            $table->string('closing_hour');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();

            $table->string('ic_number');
            $table->string('business_cert_url');

            $table->string('bank_name');
            $table->string('bank_account_name');
            $table->string('bank_account_number');

            $table->foreignIdFor(User::class);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
