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
        Schema::create('service_offered', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('technician_id');
            $table->foreign('technician_id')
                  ->references('id')
                  ->on('technician_profiles') 
                  ->onDelete('cascade');

            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')
                  ->references('id')
                  ->on('service')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_offered');
    }
};

