<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowedMediaExtensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowed_media_extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('extension');
            $table->string('mime_type');
            $table->unsignedBigInteger('media_type_id');
            $table->timestamps();

            $table->foreign('media_type_id')->references('id')->on('media_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowed_media_extensions');
        Schema::table('allowed_media_extensions', function (Blueprint $table) {
            $table->dropForeign(['media_type_id']);
        });
    }
}
