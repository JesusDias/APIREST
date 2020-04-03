<?php 

namespace Source\Models;

final Class Validations{
    public static function validationString(string $String){
        return strlen($String)>=3 && !is_numeric($String);
    }

    public static function validationEmail(string $Email){
        return filter_var($Email, FILTER_VALIDATE_EMAIL);
    }

    public static function validationInteger(string $Integer){
        return filter_var($Integer, FILTER_VALIDATE_INT);
    }
}