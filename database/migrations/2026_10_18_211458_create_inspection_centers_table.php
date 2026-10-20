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
    Schema::create('inspection_centers', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('slug')->unique();

        $table->string('logo_path')->nullable();

        $table->string('phone', 30)->nullable();
        $table->string('mobile', 30)->nullable();

        $table->string('city', 100)->nullable();
        $table->text('address')->nullable();

        $table->text('terms_text')->nullable();
        $table->string('primary_color', 20)->default('#2563eb');

        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_centers');
    }
};
