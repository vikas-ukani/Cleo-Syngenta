<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpseclib\Math\BigInteger;

class CreateCategoryContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_content_category', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('content_category_id');
            $table->unsignedBigInteger('content_id');
            $table->timestamps();

            $table->foreign('content_category_id')->references('id')->on('content_categories')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('content')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_content');
    }
}
