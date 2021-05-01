<?php

namespace App\Enums;

class AssetStatus
{
    const APPROVED = 'Approved';
    const REJECTED = 'Rejected';
    const PENDING = 'Pending';

    public static $statuses = [
        self::APPROVED, self::REJECTED
    ];
}
