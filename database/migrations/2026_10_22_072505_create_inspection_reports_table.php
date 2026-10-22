<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();

            $table->uuid('public_uuid')->unique();
            $table->string('report_number', 30)->unique();

            $table->foreignId('inspection_center_id')
                ->constrained('inspection_centers')
                ->restrictOnDelete();

            $table->foreignId('inspector_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->string('status', 30)->default('draft');

            $table->dateTime('inspection_date')->nullable();
            $table->unsignedInteger('mileage')->nullable();

            $table->text('summary')->nullable();
            $table->text('general_notes')->nullable();

            $table->string('pdf_path')->nullable();
            $table->string('pdf_hash', 64)->nullable();

            $table->dateTime('finalized_at')->nullable();
            $table->dateTime('sms_sent_at')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
            $table->index(['inspection_center_id', 'status']);
            $table->index(['inspector_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_reports');
    }
};