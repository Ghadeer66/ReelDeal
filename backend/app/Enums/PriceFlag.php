<?php

namespace App\Enums;

enum PriceFlag: string
{
    case Normal = 'normal';
    case High = 'high';
    case Low = 'low';
    case Suspicious = 'suspicious';
}
