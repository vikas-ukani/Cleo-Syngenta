<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCropTechnicalDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crop_technical_data', function (Blueprint $table) {
            // d, crop_id, business_id, country_id, meta_key, meta_value NULL, 1, 2, 3, "a_code", "A1234"
            $table->bigIncrements('id');
            $table->unsignedBigInteger('crop_id')->nullable();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('meta_key', 50)->comment('Comes from `crop_technical_key` table key name')->nullable();
            $table->string('meta_value', 200)->comment('Comes from `crop_technical_key` table key value')->nullable();

            $table->foreign('crop_id')->references('id')->on('crops');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crop_technical_data');
    }
}
