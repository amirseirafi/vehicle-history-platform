<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('vin', 17)->nullable()->unique();
            $table->string('chassis_number', 100)->nullable()->index();

            $table->string('brand', 100);
            $table->string('model', 100);
            $table->string('trim', 100)->nullable();

            $table->unsignedSmallInteger('manufacture_year_gregorian')->nullable();
            $table->unsignedSmallInteger('manufacture_year_jalali')->nullable();

            $table->string('color', 50)->nullable();
            $table->string('engine_number', 100)->nullable();
            $table->string('plate_number', 30)->nullable();

            $table->unsignedInteger('current_mileage')->nullable();

            $table->timestamps();

            $table->index(['brand', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
