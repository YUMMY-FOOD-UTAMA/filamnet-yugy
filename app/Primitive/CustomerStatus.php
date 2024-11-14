<?php

namespace App\Primitive;

enum CustomerStatus
{
    const PROSPECT = 'Prospect';
    const NEW = 'New';
    const EXISTING = 'Existing';

    public static function values(): array
    {
        return [
            self::PROSPECT,
            self::NEW,
            self::EXISTING,
        ];
    }

    public static function valuesObject(): array
    {
        $options = [
            [
                "id" => self::PROSPECT,
                "name" => self::PROSPECT
            ],
            [
                "id" => self::NEW,
                "name" => self::NEW
            ],
            [
                "id" => self::EXISTING,
                "name" => self::EXISTING
            ]
        ];

        return array_map(function ($item) {
            return (object)$item;
        }, $options);
    }

    public static function options(): array
    {
        $options = [
            self::PROSPECT => self::PROSPECT,
            self::NEW => self::NEW,
            self::EXISTING => self::EXISTING,
        ];

        return $options;
    }
}
