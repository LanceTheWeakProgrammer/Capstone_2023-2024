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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('user_profiles')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('technician_profiles')->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained('guests')->onDelete('cascade');
            $table->foreignId('vehicle_detail_id')->constrained('vehicle_details')->onDelete('cascade'); 
            $table->timestamp('booking_date');
            $table->timestamp('rescheduled_date')->nullable();
            $table->string('status')->default('Pending');
            $table->double('total_fee', 8, 2)->default(0.00);
            $table->text('additional_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
