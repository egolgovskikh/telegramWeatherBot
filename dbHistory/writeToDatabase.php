<?php

function writeToDb ($username, $latitude, $longitude) {

    $host = '127.0.0.1'; // адрес сервера
    $database = 'history'; // имя базы данных
    $user = 'root'; // имя пользователя
    $password = 'root'; // пароль

    //Соединение с БД
    $link = mysqli_connect($host, $user, $password, $database)
    or print_r("Ошибка подключения к БД");
    //die("Ошибка подключения к БД" . mysqli_error($link));

    $date = date("Y-m-d H:i:s", strtotime("+3 hours"));
    $query = "INSERT INTO `history`(`time`, `username`, `latitude`, `longitude`) VALUES ('$date', '$username', '$latitude', '$longitude')";
    $result = mysqli_query($link, $query) or print_r("Ошибка записи в БД");
    //die("Ошибка " . mysqli_error($link));
    if ($result) {
        echo "{$date} - Выполнение запроса от {$username} прошло успешно \n";
    }
    mysqli_close($link);
}
