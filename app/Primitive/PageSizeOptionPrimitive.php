<?php

namespace App\Primitive;

class PageSizeOptionPrimitive
{
    public static function getPageSizeOptions()
    {
        $options = [
            [
                "id" => 10,
                "name" => "10"
            ],
            [
                "id" => 25,
                "name" => "25"
            ],
            [
                "id" => 50,
                "name" => "50"
            ],
            [
                "id" => 100,
                "name" => "100"
            ],
        ];

        return array_map(function ($item) {
            return (object) $item;
        }, $options);
    }
}
