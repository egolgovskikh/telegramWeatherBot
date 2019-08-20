<?php

use GuzzleHttp\Client;

class Weather
{
    protected $token = "37cab41817a84b1f8bc159be9f1f0f8a";

    public function getWeather($lat, $lon)
    {
        $url = "api.openweathermap.org/data/2.5/weather";

        $params = [];
        $params['lat'] = $lat;
        $params['lon'] = $lon;
        $params['APPID'] = $this->token;

        $url .= "?" . http_build_query($params);

        $client = new Client([
            'base_uri' => $url
        ]);

        $result = $client->request('GET');

        return json_decode($result->getBody());
    }

}
