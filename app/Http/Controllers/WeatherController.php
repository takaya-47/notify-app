<?php

namespace App\Http\Controllers;

use App\Models\Weather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\Client\Response;
use Exception;

/**
 * 天気予報APIへのリクエスト、レスポンスを処理するコントローラー
 */
class WeatherController extends Controller
{
    // 定数の定義
    const GEOCODING_API = 'http://api.openweathermap.org/geo/1.0/direct';
    const WEATHER_API   = 'http://api.openweathermap.org/data/2.5/forecast';
    const LINE_NOTIFY_URL = 'https://notify-api.line.me/api/notify';

    /**
     * APIから天気予報を取得し、通知します
     *
     * @return void
     */
    public function notice(): void
    {
        // 天気予報をAPIから取得
        $response = $this->fetch_weather_forecast();
        // レスポンスのエラー有無をチェックする。エラーがあればレスポンス返却。
        $this->check_response_error($response);

        // DB登録、通知処理
        try {
            $weather = new Weather();
            // weatherテーブルへのデータ格納
            $weather->insert_into_weather($response['list'], $response['city']['name']);
            // 通知用のレコード取得
            $weather_data = $weather->fetch_by_date(date('Y-m-d'));
            // LINE通知実行
            Http::withToken(env('LINE_NOTIFY_API_TOKEN'))->asForm()->post(
                self::LINE_NOTIFY_URL,
                [
                    'message' => 'テストだよ' // TODO: ここを実際に送信する天気情報のテキストにする。画像も送れる？
                ]
            );
        } catch (Exception $e) {
            Log::error('予期せぬエラーが発生しました。', [__METHOD__, 'LINE:' . __LINE__ . $e]);
        }
    }

    /**
     * ジオコーディングAPIで取得した緯度経度を基に天気予報を取得します
     *
     * @return array
     */
    public function fetch_weather_forecast(): array
    {
        $geographic_data = $this->fetch_lat_and_lon();
        // レスポンスのエラー有無をチェックする。エラーがあればレスポンス返却。
        $this->check_response_error($geographic_data);

        // 天気予報取得APIにリクエストし、レスポンスを受け取る
        // APIを叩いた日の06:00:00から24:00:00（翌日00:00:00）までの天気予報を取得
        $response_json = Http::get(
            self::WEATHER_API,
            [
                'appid' => env('OPEN_WEATHER_MAP_API_KEY'),
                'lat'   => $geographic_data['lat'],
                'lon'   => $geographic_data['lon'],
                'lang'  => 'ja',
                'units' => 'metric',
                'cnt'   => 7
            ]
        );
        if ($response_json->successful()) {
            // ステータスコードが200以上300未満の時。
            $response = json_decode($response_json->body(), true);
            // 天気予報情報を丸ごと取得して返却。加工は別メソッドで行う。
            return $response;
        } else {
            // 成功以外の場合はログを出した上でエラーコードとメッセージを返却
            $this->make_error_log($response_json);
            return $this->response_when_api_error(json_decode($response_json->body(), true));
        }
    }

    /**
     * ジオコーディングAPIを用いて特定都市の緯度と経度を取得します
     *
     * @return array
     */
    public function fetch_lat_and_lon(): array
    {
        // ジオコーディングAPIにリクエストし、レスポンスを受け取る
        $response_json = Http::get(
            self::GEOCODING_API,
            [
                'appid' => env('OPEN_WEATHER_MAP_API_KEY'),
                'q'     => 'Komoro',
                'limit' => 1
            ]
        );

        if ($response_json->successful()) {
            $response = json_decode($response_json->body(), true);
            // ステータスコードが200以上300未満の時。必要なデータのみに絞って返却。
            return [
                'lat' => $response[0]['lat'], // 緯度
                'lon' => $response[0]['lon'] // 経度
            ];
        } else {
            // 成功以外の場合はログを出した上でエラーコードとメッセージを返却
            $this->make_error_log($response_json);
            return $this->response_when_api_error(json_decode($response_json->body(), true));
        }
    }

    /**
     * APIのレスポンスにエラーがあるか判定します
     * エラーがあればレスポンスを返却します
     *
     * @param  mixed $response_data
     * @return mixed
     */
    public function check_response_error(array $response_data)
    {
        if (!empty($response_data['error_code']) || !empty($response_data['error_message'])) {
            Log::error('取得データにエラーがあるため処理を中断しました。', $response_data);
            return $response_data;
        }
        return;
    }

    /**
     * レスポンスエラー時のログ出力を行います
     *
     * @param  Response $response_json
     * @return void
     */
    public function make_error_log(Response $response_json): void
    {
        if ($response_json->clientError()) {
            // 400レベルのステータスコード
            Log::error('[ジオコーディングAPIのエラー]クライアント側でエラーが発生しました。', [__METHOD__, 'LINE:' . __LINE__, json_decode($response_json->body(), true)]);
        } elseif ($response_json->serverError()) {
            // 500レベルのステータスコード
            Log::error('[ジオコーディングAPIのエラー]サーバー側でエラーが発生しました。', [__METHOD__, 'LINE:' . __LINE__, json_decode($response_json->body(), true)]);
        }
    }

    /**
     * レスポンスエラー時のエラーコードとメッセージを配列にして返却します
     *
     * @param  array $response_data
     * @return array
     */
    public function response_when_api_error(array $response_data): array
    {
        return [
            'error_code'    => $response_data['cod'],
            'error_message' => $response_data['message']
        ];
    }

}
