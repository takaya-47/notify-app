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
        // weatherテーブルのdateカラムのデータ型をdateからdatetimeに変更する
        Schema::table('weather', function (Blueprint $table) {
            $table->datetime('date')->comment('日時')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weather', function (Blueprint $table) {
            $table->date('date')->comment('日付')->change();
        });
    }
};
