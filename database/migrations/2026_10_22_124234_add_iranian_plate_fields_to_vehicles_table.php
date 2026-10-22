<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('plate_province_code', 2)
                ->nullable()
                ->after('plate_number');

            $table->string('plate_three_digits', 3)
                ->nullable()
                ->after('plate_province_code');

            $table->string('plate_letter', 5)
                ->nullable()
                ->after('plate_three_digits');

            $table->string('plate_two_digits', 2)
                ->nullable()
                ->after('plate_letter');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'plate_province_code',
                'plate_three_digits',
                'plate_letter',
                'plate_two_digits',
            ]);
        });
    }
};