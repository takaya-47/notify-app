<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Weather;

/**
 * 天気予報モデルに対応するコントローラー
 */
class WeatherController extends Controller
{
    /**
     * 天気予報を取得します
     *
     * @return void
     */
    public function store(): void
    {
        $weather = new Weather();
        $weather->store_response_data_from_weather_api();
    }
}
