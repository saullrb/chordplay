<?php

declare(strict_types=1);

namespace App\Enums;

enum FlashType: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
    case INFO = 'info';
}
