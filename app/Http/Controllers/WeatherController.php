<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Weather;

/**
 * 天気予報APIへのリクエスト、レスポンスを処理するコントローラー
 */
class WeatherController extends NoticeController
{
    /**
     * 天気予報を取得し、DB登録します
     *
     * @return void
     */
    public function store(): void
    {
        $weather = new Weather();
        $weather->store_response_data_from_weather_api();
    }
}
