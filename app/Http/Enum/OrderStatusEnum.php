<?php

namespace App\Http\Enum;

enum OrderStatusEnum: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
