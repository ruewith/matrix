<?php
    $server = "localhost";
    $user = "root";
    $password = "";
    $dbname = "stringDB";

    $connect = new mysqli($server, $user, $password);

    if($connect->connect_error){
        die("Connection failed:".mysqli_connect_error());
    }

    $createdb ="CREATE DATABASE stringDB";


    if ($connect->query($createdb) === FALSE) {
      echo "Ошибка создания БД: " . $connect->error;
    }

    $connect->close();

    $connect = new mysqli($server, $user, $password, $dbname);
    if ($connect->connect_error) {
        die("Ошибка подключения: " . $connect->connect_error);
    }

    $createtb = "CREATE TABLE stringValue (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,value VARCHAR(300) NOT NULL)";
    if ($connect->query($createtb) === FALSE) {
      echo "Ошибка создания таблицы: " . $connect->error;
    }

    $string = '{Пожалуйста,|Просто|Если сможете,} сделайте так, чтобы это {удивительное|крутое|простое|важное|бесполезное} тестовое предложение {изменялось {быстро|мгновенно|оперативно|правильно} случайным образом|менялось каждый раз}.';

    function match($string){
        if (!preg_match_all('/{([^{}]+)}/', $string, $matches)) {
            return $string;
        }

        $arr = [];
        foreach ($matches[1] as $k => $match) {
            $searchTerm = $matches[0][$k];

            if (!array_key_exists($k, $arr)) {
                $arr[$k] = [];
            }

            $e = explode('|', $match);

            foreach ($e as $v) {

                $buffer = str_replace($searchTerm, str_replace($match, $v, $match), $string);

                if (strpos($buffer, "{") !== false) {
                    $arr[$k][] = match($buffer);
                } else {
                    $arr[$k][] = $buffer;
                }
            }
        }
        return $arr;
    }

    $match = match($string);
    $flatten = new RecursiveIteratorIterator(new RecursiveArrayIterator($match));
    $values = [];
    foreach ($flatten as $v) {

        if (!in_array($v, $values)) {
            $values[] = $v;
        }
    }

    //var_dump($values);
    $sql = "";

    for($i = 0;$i<count($values);$i++){
        $sql .= "INSERT INTO stringValue (value) VALUES ('$values[$i]');";
    }

    $sql = rtrim($sql, ";");

    //print_r($sql);

    $connect->multi_query($sql);

    $connect->close();
