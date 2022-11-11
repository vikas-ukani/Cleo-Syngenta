<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageAndDescriptionColumnsToDiseasesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('diseases', function (Blueprint $table) {
      $table->longText('description')->nullable()->after('name');
      $table->string('image')->nullable()->after('name');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('diseases', function (Blueprint $table) {
      $table->dropColumn('description');
      $table->dropColumn('image');
    });
  }
}
