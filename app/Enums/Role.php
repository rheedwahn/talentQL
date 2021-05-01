<?php

namespace App\Enums;

class Role
{
    const ADMIN = 'Admin';
    const PHOTOGRAPHER = 'Photographer';
    const CUSTOMER = 'Customer';

    public static $roles = [
        self::ADMIN, self::PHOTOGRAPHER, self::CUSTOMER
    ];
}
