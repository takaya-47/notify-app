<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weather', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('日付');
            $table->tinyText('prefecture')->comment('都道府県');
            $table->tinyText('municipalities')->comment('市町村');
            $table->tinyText('weather')->comment('天気');
            $table->tinyInteger('highest_temperature')->comment('最高気温');
            $table->tinyInteger('lowest_temperature')->comment('最低気温');
            $table->tinyInteger('humidity')->unsigned()->comment('湿度');
            $table->tinyInteger('rainy_percent')->unsigned()->comment('降水確率');
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
        Schema::dropIfExists('weather');
    }
};
