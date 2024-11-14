<?php

namespace App\Primitive;

enum SalesMappingStatus
{
    const WAITING_APPROVAL = 'Waiting Approval';
    const APPROVED = 'Approved';
    const IN_PROGRESS = 'In Progress';
    const EXPIRED = 'Expired';
    const CANCELLED = 'Cancelled';
    const REJECTED = 'Rejected';

    public static function values(): array
    {
        return [
            self::WAITING_APPROVAL,
            self::APPROVED,
            self::IN_PROGRESS,
            self::EXPIRED,
            self::CANCELLED,
            self::REJECTED,
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
                "id" => self::EXPIRED,
                "name" => self::EXPIRED
            ],
            [
                "id" => self::CANCELLED,
                "name" => self::CANCELLED
            ],
            [
                "id" => self::REJECTED,
                "name" => self::REJECTED
            ],
        ];

        return array_map(function ($item) {
            return (object)$item;
        }, $options);
    }

    public static function statusAvailableForBooking(): array
    {
        return [
            self::CANCELLED,
            self::REJECTED,
            self::EXPIRED,
        ];
    }

    public static function getStatusColor(string $status): string
    {
        switch ($status) {
            case self::APPROVED:
                return 'success';
            case self::REJECTED:
            case self::CANCELLED:
                return 'danger';
            case self::WAITING_APPROVAL:
                return 'warning';
            default:
                return 'secondary';
        }
    }
}
