<?php

namespace App\Enums;

enum UserType: string
{
    case Guest = 'guest';
    case Customer = 'customer';
    case Seller = 'seller';
    case Merchant = 'merchant';
    case Admin = 'admin';
}
