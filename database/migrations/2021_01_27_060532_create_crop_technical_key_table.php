<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCropTechnicalKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crop_technical_key', function (Blueprint $table) {
            // crop_technical_key id, key, label NULL, "a_code", "A Code"
            $table->bigIncrements('id');
            $table->string('key', 100)->comment('key name ');
            $table->string('label', 100)->comment('ket title label');
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
        Schema::dropIfExists('crop_technical_key');
    }
}
