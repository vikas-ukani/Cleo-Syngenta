<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessCropTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('business_crop', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('business_id');
      $table->unsignedBigInteger('crop_id');
      $table->timestamps();

      $table->foreign('business_id')->references('id')->on('businesses');
      $table->foreign('crop_id')->references('id')->on('crops');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('business_crop');
  }
}
