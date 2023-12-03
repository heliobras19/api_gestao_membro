<?php

namespace App\Services;

class APIResponse
{
    public static function response($data, $sucess = true)
    {
        return [
            "success" => $sucess,
            "data" => $data
        ];
    }
}
