<?php

namespace App\Http\Helpers;

class WeatherHelper{
    public static function convertWindDeg(int $wind_deg) : string{
        $windDirections = ['Cеверный', 'Cеверо-Восточный', 'Восточный', 'Юго-Восточный', 'Южный', 'Юго-Западный', 'Западный', 'Северо-Западный'];
        $directionIndex = (int)($wind_deg + 22.5) / 45 % 8;

        return $windDirections[$directionIndex];
    }
}
