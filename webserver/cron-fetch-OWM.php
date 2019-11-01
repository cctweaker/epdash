<?php

$lat = 44.49; // your latitude
$lon = 26.03; // your longitude, get them from Google Maps.

$key = "";    // your OpenWeatherMap API key

// use linux 'crontab -e' command and set up a cron job as follows:
// 1,31 * * * *         wget -O - http://127.0.0.1/meteo/cron-fetch-OWM.php >/dev/null 2>&1

// remember, cron user should have write access to current folder!
// 1,31 means it fetches the json at minute 1 and 31 each our. This ensures you don't strain OWM servers and anyway it gets udpated at minute 00 and 30 so there is no sense in more frequent requsts.



$link_weather = "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&lang=en&units=metric&appid=$key";
$weather = file_get_contents($link_weather);
file_put_contents("weather.json", $weather);



$link_forecast = "http://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&lang=en&units=metric&appid=$key";
$forecast = file_get_contents($link_forecast);
file_put_contents("forecast.json", $forecast);



$link_forecast_uv = "http://api.openweathermap.org/data/2.5/uvi/forecast?lat=$lat&lon=$lon&appid=$key";
$forecast_uv = file_get_contents($link_forecast_uv);
file_put_contents("forecast_uv.json", $forecast_uv);



$link_uv = "http://api.openweathermap.org/data/2.5/uvi?lat=$lat&lon=$lon&appid=$key";
$uv = file_get_contents($link_uv);
file_put_contents("uv.json", $uv);


exit;
