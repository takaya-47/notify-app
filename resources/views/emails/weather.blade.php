<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>{{ $weather_data_array[0]->municipalities }}の３時間毎の天気です</h1>

    @foreach ($weather_data_array as $weather_data)
        <h2>{{ date('G', strtotime($weather_data->date)) }}時</h2>
        <p>天候: {{ $weather_data->weather }}</p>
        <p>最高気温: {{ $weather_data->highest_temperature }}℃</p>
        <p>最低気温: {{ $weather_data->lowest_temperature }}℃</p>
        <p>湿度: {{ $weather_data->humidity }}%</p>
        <p>降水確率: {{ $weather_data->rainy_percent }}%</p>
    @endforeach

    <p>以上です。</p>
</body>
</html>
