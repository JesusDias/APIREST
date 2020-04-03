<?php 

define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "bancoapirest",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // se ele vai ou não mostrar as exceções, nesse caso ele vai
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // vai converter qualquer resultado em objetos, deixando totalmente orientado a objetos
        PDO::ATTR_CASE => PDO::CASE_NATURAL // vai alterar os cases para cases normais, ou seja, vai alterar de maiúsculas para normais
    ]
]);