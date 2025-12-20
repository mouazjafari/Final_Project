<?php

namespace App\Http\Enum;

enum RoleUserEnum: string
{
    case Guest = 'guest';
    case User = 'user';
    case Admin = 'admin';
    case SuperAdmin = 'superadmin';
}
