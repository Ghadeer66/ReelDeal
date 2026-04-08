<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Disputed = 'disputed';
    case Refunded = 'refunded';
    case Completed = 'completed';
}
