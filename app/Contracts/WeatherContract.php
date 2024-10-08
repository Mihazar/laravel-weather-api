<?php

namespace App\Contracts;

interface WeatherContract {

    function getCityCoordinates(string $city_name) : array | \Exception;

    public function getWeatherForecast(string $city_name) : array;

    public function getPreparedData(array $weather_forecast) : array;
}
