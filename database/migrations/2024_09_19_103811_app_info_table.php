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
        Schema::create('app_info', function (Blueprint $table) {
            $table->id('appID');
            $table->string('appTitle');
            $table->text('appAbout');
            $table->timestamps();
        });

        $appData = [
            'appTitle' => 'JHPaints',
            'appAbout' =>'JHPaints delivers expert maintenance and repair solutions, ensuring your vehicle operates at peak performance and reliability.',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        DB::table('app_info')->insert($appData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_info');
    }
};
