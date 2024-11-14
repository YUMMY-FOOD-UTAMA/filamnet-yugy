<?php

namespace App\Primitive;

enum SalesMappingStatus
{
    const WAITING_APPROVAL = 'Waiting Approval';
    const APPROVED = 'Approved';
    const IN_PROGRESS = 'In Progress';
    const EXPIRED = 'Expired';
    const DONE = 'Done';
    const CANCELLED = 'Cancelled';

    public static function values(): array
    {
        return [
            self::WAITING_APPROVAL,
            self::APPROVED,
            self::IN_PROGRESS,
            self::EXPIRED,
            self::DONE,
            self::CANCELLED,
        ];
    }

    public static function valuesObject(): array
    {
        $options = [
            [
                "id" => self::WAITING_APPROVAL,
                "name" => self::WAITING_APPROVAL
            ],
            [
                "id" => self::APPROVED,
                "name" => self::APPROVED
            ],
            [
                "id" => self::IN_PROGRESS,
                "name" => self::IN_PROGRESS
            ],
            [
                "id" => self::DONE,
                "name" => self::DONE
            ],
            [
                "id" => self::EXPIRED,
                "name" => self::EXPIRED
            ],
            [
                "id" => self::CANCELLED,
                "name" => self::CANCELLED
            ],
        ];

        return array_map(function ($item) {
            return (object)$item;
        }, $options);
    }
}
