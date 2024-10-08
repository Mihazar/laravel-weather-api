<?php

namespace App\Http\Services;

use App\Contracts\WeatherContract;
use App\Http\Helpers\WeatherHelper;
use GuzzleHttp\Client;

class WeatherService implements WeatherContract {

    const MM_IN_PHA = 0.75;
    const KELVIN = 273.15;

    public function __construct()
    {
        $this->guzzleClient = new Client();
        $this->weather_api_url = 'https://ru.api.openweathermap.org';
        $this->api_key = env('WEATHERMAP_API_KEY');
    }

    public function getPreparedData(array $weather_forecast): array
    {
        $celsiusTemp = $weather_forecast['main']['temp'] - self::KELVIN;
        $fahrenheitTemp = 1.8 * $celsiusTemp + 32;

        return [
            'weather' => [
                'cloud_cover' => $weather_forecast['weather'][0]['main'],
                'cloud_cover_description' => $weather_forecast['weather'][0]['description'],
                'humidity' => $weather_forecast['main']['humidity'],
                'pressure' => [
                    'pHa' => $weather_forecast['main']['pressure'],
                    'mm' => $weather_forecast['main']['pressure'] * self::MM_IN_PHA
                ],
                'temp' => [
                    'celsius' => (float)number_format($celsiusTemp, 2, '.', ''),
                    'fahrenheit' => (float)number_format($fahrenheitTemp, 2, '.', '')
                ]
            ],
            'wind' => [
                'wind_speed' => $weather_forecast['wind']['speed'],
                'deg' => $weather_forecast['wind']['deg'],
                'direction' => WeatherHelper::convertWindDeg($weather_forecast['wind']['deg'])
            ]
        ];
    }

    public function getWeatherForecast(string $city_name) : array{
        list($lon, $lat) = $this->getCityCoordinates($city_name);

        $url = $this->weather_api_url.'/data/2.5/weather';

        try{
            $query = $this->guzzleClient->request('GET', $url, [
                'connect_timeout' => 7,
                'query' => [
                    'lon' => $lon,
                    'lat' => $lat,
                    'appid' => $this->api_key
                ]
            ])->getBody();

            $response = json_decode($query, true);

        } catch (\Exception $exception){
            throw new \Exception('Произошла ошибка запроса:' . $exception->getMessage());
        }

        if(empty($response)){
            throw new \Exception('Произошла ошибка запроса:' . $response);
        }

        return $response;
    }

    function getCityCoordinates(string $city_name) : array | \Exception
    {

        if($this->api_key == ''){
            throw new \Exception('Не задан API-ключ');
        }

        $url = $this->weather_api_url.'/geo/1.0/direct';

        try{
            $query = $this->guzzleClient->request('GET', $url, [
                'connect_timeout' => 7,
                'query' => [
                    'q' => $city_name,
                    'limit' => 1,
                    'appid' => $this->api_key
                ]
            ])->getBody();

            $response = json_decode($query);
        } catch (\Exception $exception){
            throw new \Exception('Произошла ошибка запроса:' . $exception->getMessage());
        }

        if(empty($response)){
            throw new \Exception('Произошла ошибка запроса:' . $response);
        }

        return [
            $response[0]->lat,
            $response[0]->lon
        ];
    }
}
