<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdColumnOnBusinessesTableToUnsigned extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('businesses', function (Blueprint $table) {
      $table->unsignedBigInteger('id')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('businesses', function (Blueprint $table) {
      $table->bigInteger('id')->change();
    });
  }
}
