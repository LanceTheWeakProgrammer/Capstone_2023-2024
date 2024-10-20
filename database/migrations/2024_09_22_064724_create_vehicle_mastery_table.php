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
        Schema::create('vehicle_mastery', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('technician_id');
            $table->foreign('technician_id')
                  ->references('id')
                  ->on('technician_profiles') 
                  ->onDelete('cascade');

            $table->unsignedBigInteger('vehicle_type_id');
            $table->foreign('vehicle_type_id')
                  ->references('id')
                  ->on('vehicle_types') 
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_mastery');
    }
};
