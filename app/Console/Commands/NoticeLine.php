<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WeatherController;

class NoticeLine extends Command
{
    /**
     * コマンドの名前(sail artisan コマンド名 で実行できる)
     *
     * @var string
     */
    protected $signature = 'noticeline';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = 'LINEに天気予報を通知する';

    /**
     * コマンドの実行処理
     *
     * @return void
     */
    public function handle()
    {
        $controller = new WeatherController();
        $controller->notice();
    }
}
