<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCropCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_crop', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('crop_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            $table->foreign('crop_id')->references('id')->on('crops');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_crop');
    }
}
