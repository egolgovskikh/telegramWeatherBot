<?php

include("vendor/autoload.php");
include('TelegramBot.php');
include("Weather.php");
include ("dbHistory/writeToDatabase.php");

//Получаем сообщения
$telegramApi = new TelegramBot();

$weatherApi = new Weather();


while (true) {

    sleep(2);

    $updates = $telegramApi->getUpdates();

    //По каждому сообщению пробегаемся
    foreach ($updates as $update) {

        if (isset($update->message->location)) {

            //Получаем погоду
            $result = $weatherApi->getWeather($update->message->location->latitude, $update->message->location->longitude);

            //Температура
            $temperature = round($result->main->temp - 273.15);
            if ($temperature > 0) {
                $response = "Температура: +" . $temperature;
            } elseif ($temperature < 0) {
                $response = "Температура: -" . $temperature;
            } else {
                $response = "Температура: " . $temperature;
            }

            //Осадки
            switch ($result->weather[0]->main) {
                case "Clear":
                    $response .= "\n\xE2\x98\x80 На улице безоблачно";
                    break;
                case "Clouds":
                    $response .= "\n\xE2\x98\x81 На улице облачно";
                    break;
                case "Rain":
                    $response .= "\n\xE2\x98\x94 На улице дождь";
                    break;
                case "Mist":
                    $response .= "\n\xF0\x9F\x8C\x81 На улице туман";
                    break;
                case "Haze":
                    $response .= "\n\xF0\x9F\x8C\x81 На улице туман";
                    break;
                default:
                    $response .= $result->weather[0]->main;

            }

            //Ветер
            $wind = $result->wind->speed;
            if ($wind < 0.2) {
                $response .= "\n\xF0\x9F\x92\xA8 Безветренно (" . $wind . "м/с)";
            } elseif ($wind < 1.5) {
                $response .= "\n\xF0\x9F\x92\xA8 Тихий ветер (" . $wind . "м/с)";
            } elseif ($wind < 3.3) {
                $response .= "\n\xF0\x9F\x92\xA8 Легкий ветер (" . $wind . "м/с)";
            } elseif ($wind < 5.4) {
                $response .= "\n\xF0\x9F\x92\xA8 Слабый ветер (" . $wind . "м/с)";
            } elseif ($wind < 7.9) {
                $response .= "\n\xF0\x9F\x92\xA8 Умеренный ветер (" . $wind . "м/с)";
            } elseif ($wind < 10.7) {
                $response .= "\n\xF0\x9F\x92\xA8 Свежий ветер (" . $wind . "м/с)";
            } elseif ($wind < 13.8) {
                $response .= "\n\xF0\x9F\x92\xA8 Сильный ветер (" . $wind . "м/с)";
            } elseif ($wind < 17.1) {
                $response .= "\n\xF0\x9F\x92\xA8 Крепкий ветер (" . $wind . "м/с)";
            } elseif ($wind < 20.7) {
                $response .= "\n\xF0\x9F\x92\xA8 Очень крепкий ветер (" . $wind . "м/с)";
            } elseif ($wind < 24.4) {
                $response .= "\n\xF0\x9F\x92\xA8 Шторм (" . $wind . "м/с)";
            } elseif ($wind < 28.4) {
                $response .= "\n\xF0\x9F\x92\xA8 Сильный шторм (" . $wind . "м/с)";
            } elseif ($wind < 32.6) {
                $response .= "\n\xF0\x9F\x92\xA8 Жестокий шторм (" . $wind . "м/с)";
            } else {
                $response .= "\n\xF0\x9F\x92\xA8 Ураган (" . $wind . "м/с)";
            }

            //Закат, Рассвет
            $sunset = $result->sys->sunset;
            $sunset = date("H:i", $sunset);
            $sunrise = $result->sys->sunrise;
            $sunrise = date("H:i", $sunrise);
            $response .= "\n\xF0\x9F\x8C\x86 Закат в " . $sunset . "\n\xF0\x9F\x8C\x85 Рассвет в " . $sunrise;

            //Местоположение
            $location = $result->name;
            $response .= "\nВы находитесь в: " . $location;
            //Страна:
            $country = $result->sys->country;

            if ($country === "RU") {
                $response .= " \xF0\x9F\x87\xB7\xF0\x9F\x87\xBA";
            } elseif ($country === "DE") {
                $response .= " \xF0\x9F\x87\xA9\xF0\x9F\x87\xAA";
            } elseif ($country === "GB") {
                $response .= " \xF0\x9F\x87\xAC\xF0\x9F\x87\xA7";
            } elseif ($country === "CN") {
                $response .= " \xF0\x9F\x87\xA8\xF0\x9F\x87\xB3";
            } elseif ($country === "JP") {
                $response .= " \xF0\x9F\x87\xAF\xF0\x9F\x87\xB5";
            } elseif ($country === "FR") {
                $response .= " \xF0\x9F\x87\xAB\xF0\x9F\x87\xB7";
            } elseif ($country === "KR") {
                $response .= " \xF0\x9F\x87\xB0\xF0\x9F\x87\xB7";
            } elseif ($country === "ES") {
                $response .= " \xF0\x9F\x87\xAA\xF0\x9F\x87\xB8";
            } elseif ($country === "IT") {
                $response .= " \xF0\x9F\x87\xAE\xF0\x9F\x87\xB9";
            } elseif ($country === "US") {
                $response .= " \xF0\x9F\x87\xBA\xF0\x9F\x87\xB8	";
            } else {
                $response .= " " . $country . "";
            }

            //На каждое сообщение отвечаем
            $telegramApi->sendMessage($update->message->chat->id, $response);

            //Запись в бд
            $username = $update->message->chat->username;
            $latitude = $update->message->location->latitude;
            $longitude = $update->message->location->longitude;

            writeToDb($username, $latitude, $longitude);

        } elseif ($update->message->text === "/location") {
            //команда /location
            $telegramApi->sendMessage($update->message->chat->id, "\xF0\x9F\x8C\x8D");
        } elseif ($update->message->text === "/description") {
            //команда /location
            $telegramApi->sendMessage(
                $update->message->chat->id,
                "Бот для определения погоды. Чтобы отправить локацию нажмите на \"Прикрепить\" (скрепка) -> \"Местоположение\"");
        } else {
            //На каждое сообщение отвечаем
            $telegramApi->sendMessage($update->message->chat->id, "Отправьте местоположение \xF0\x9F\x8C\x8D");
        }

    }

}

