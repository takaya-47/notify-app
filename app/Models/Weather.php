<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\Client\Response;

class Weather extends Model
{
    use HasFactory;

    // 使用するAPIのURL
    const GEOCODING_API = 'http://api.openweathermap.org/geo/1.0/direct';
    const WEATHER_API   = 'http://api.openweathermap.org/data/2.5/forecast';

    /**
     * 指定した都市の緯度と経度を取得します
     *
     * @return array
     */
    private function fetch_lat_and_lon(): array
    {
        // ジオコーディングAPIにリクエストし、レスポンスを受け取る
        $response = Http::get(
            self::GEOCODING_API,
            [
                'appid' => env('OPEN_WEATHER_MAP_API_KEY'),
                'q' => 'Komoro',
                'limit' => 1
            ]
        );

        if ($response->successful()) {
            // ステータスコードが200以上300未満の時。必要なデータのみに絞って返却。
            return [
                'lat' => $response['lat'], // 緯度
                'lon' => $response['lon'] // 経度
            ];
        } else {
            // 成功以外の場合はログを出した上でエラーコードとメッセージを返却
            $this->make_error_log($response);
            $this->return_response_error($response);
        }
    }

    /**
     * fetch_weather_forecast_data
     *
     * @return void
     */
    private function fetch_weather_forecast()
    {
        $geographic_data = $this->fetch_lat_and_lon();
        // ジオコーディングAPIで正しく地理情報を取得できなければログ出力して処理終了
        if (empty($geographic_data['lat']) || empty($geographic_data['lon'])) {
            Log::error('[天気予報取得APIのエラー]地理情報を取得できなかったため天気予報の取得ができませんでした。', $geographic_data);
            return;
        }

        // 天気予報取得APIにリクエストし、レスポンスを受け取る
        // APIを叩いた日の00:00:00から翌日の24:00:00までの天気予報を取得
        // 例）6/21に叩く→6/21 00:00:00 ~ 6/23 00:00:00 (6/22 24:00:00)までを取得
        $response = Http::get(
            self::WEATHER_API,
            [
                'appid' => env('OPEN_WEATHER_MAP_API_KEY'),
                'lat' => $geographic_data['lat'],
                'lon' => $geographic_data['lon'],
                'lang' => 'ja',
                'units' => 'metric',
                'cnt' => 17
            ]
        );

        if ($response->successful()) {
            // ステータスコードが200以上300未満の時。
            return [

            ];
        } else {
            // 成功以外の場合はログを出した上でエラーコードとメッセージを返却
            $this->make_error_log($response);
            $this->return_response_error($response);
        }
    }

    /**
     * レスポンスエラー時のログ出力を行います
     *
     * @param  Response $response
     * @return void
     */
    private function make_error_log(Response $response): void
    {
        if ($response->clientError()) {
            // 400レベルのステータスコード
            Log::error('[ジオコーディングAPIのエラー]クライアント側でエラーが発生しました', [$response->getStatusCode, $response->json]);
        } elseif ($response->serverError()) {
            // 500レベルのステータスコード
            Log::error('[ジオコーディングAPIのエラー]サーバー側でエラーが発生しました', [$response->getStatusCode, $response->json]);
        }
        return;
    }

    /**
     * レスポンスエラー時のエラーコードとメッセージを配列にして返却します
     *
     * @param  Response $response
     * @return array
     */
    private function return_response_error(Response $response): array
    {
        return [
            'code' => $response['cod'],
            'message' => $response['message']
        ];
    }
}
