<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('mobile', 20)->index();

            $table->string('relation_to_vehicle', 30)
                ->default('owner');

            $table->boolean('sms_consent')->default(false);
            $table->boolean('data_consent')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['full_name', 'mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};