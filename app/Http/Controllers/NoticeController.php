<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * ユーザーへの通知処理に関するコントローラー
 */
class NoticeController extends Controller
{
    const LINE_NOTIFY_URL = 'https://notify-api.line.me/api/notify';

    /**
     * LINE NOTIFYを利用してLINEグループに通知を実行します
     *
     * @return array
     */
    public function notice(): array
    {
        $response_json = Http::withToken(env('LINE_NOTIFY_API_TOKEN'))->asForm()->post(
            self::LINE_NOTIFY_URL,
            [
                'message' => 'テストだよ'
            ]
        );

        if ($response_json->successful()) {
            // ステータスコードが200以上300未満の時
            return json_decode($response_json->body(), true);
        } else {
            // TODO:リファクタする
            return json_decode($response_json->body(), true);
        }
    }
}
