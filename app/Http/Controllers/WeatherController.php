<?php

namespace App\Http\Controllers;

use App\Contracts\WeatherContract;
use App\Http\Helpers\WeatherHelper;
use App\Http\Requests\GetWeatherRequest;
use Illuminate\Http\Response;

class WeatherController extends Controller
{
    public function __construct(protected WeatherContract $weatherContract, protected WeatherHelper $weatherHelper){}

    public function show(GetWeatherRequest $request){
        $data = $request->validated();

        $weatherForecast = $this->weatherContract->getWeatherForecast($data['city_name']);

        $preparedWeatherForecast = $this->weatherContract->getPreparedData($weatherForecast);

        return response()->json($preparedWeatherForecast, Response::HTTP_OK);
    }
}
