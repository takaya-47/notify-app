<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * 天気予報モデル
 */
class Weather extends Model
{
    use HasFactory;

    // モデルに対応するテーブル名を設定（Laravelのデフォルトはクラスの複数形のスネークケース）
    protected $table = 'weather';

    // 複数代入可能な項目を指定
    protected $fillable = [
        'date',
        'municipalities',
        'weather',
        'highest_temperature',
        'lowest_temperature',
        'humidity',
        'rainy_percent'
    ];

    /**
     * weatherテーブルにレコードを1件insertします
     *
     * @param  array $weather_data_array 天気情報
     * @param  string $city_data 都市名
     * @return void
     */
    public function insert_into_weather(array $weather_data_array, string $city_data): void
    {
        foreach ($weather_data_array as $weather_data) {
            try {
                DB::beginTransaction();
                Weather::create([
                    'date'                => $weather_data['dt_txt'],                    // 日時
                    'municipalities'      => $city_data,                                 // 市区町村
                    'weather'             => $weather_data['weather'][0]['description'], // 天気
                    'highest_temperature' => $weather_data['main']['temp_max'],          // 最高気温
                    'lowest_temperature'  => $weather_data['main']['temp_min'],          // 最低気温
                    'humidity'            => $weather_data['main']['humidity'],          // 湿度
                    'rainy_percent'       => $weather_data['pop'] * 100                  // 降水確率
                ]);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('DB登録中に予期せぬエラーが発生しました。', [__METHOD__, 'LINE:' . __LINE__ . $e]);
                exit;
            }
        }
    }

    /**
     * 日付からレコードを検索して取得します
     * ※深夜3時のレコードを除いて取得しています
     *
     * @param  string $date
     * @return Collection
     * @throws Exception
     */
    public function fetch_by_date(string $date): Collection
    {
        return DB::table($this->table)
                    ->where('date', 'like', "{$date}%")
                    ->offset(1)
                    ->limit(6)
                    ->get();
    }
}
