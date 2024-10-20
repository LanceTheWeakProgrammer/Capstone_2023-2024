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
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id('contactID');
            $table->string('address');
            $table->string('gmap');
            $table->string('tel1');
            $table->string('tel2');
            $table->string('email');
            $table->string('twt');
            $table->string('fb');
            $table->string('ig');
            $table->string('iframe')->nullable();
            $table->timestamps();
        });

        $contactData = [
            'address' => 'Tumulak St., Opon, Lapu-Lapu, Cebu',
            'gmap' => 'https://maps.app.goo.gl/gTXyk6FDeXhVrkxC9',
            'tel1' => '0928-739-8065',
            'tel2' => '0908-596-6002',
            'email' => 'lancejavate2002@gmail.com',
            'twt' => 'twitter.com',
            'fb' => 'facebook.com',
            'ig' => 'instagram.com',
            'iframe' => 'https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d245.3447381312554!2d123.94789185635645!3d10.300541257466268!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sph!4v1726748815006!5m2!1sen!2sph'
        ];

        DB::table('contact_info')->insert($contactData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
