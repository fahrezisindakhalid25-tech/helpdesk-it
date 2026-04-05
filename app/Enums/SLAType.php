<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SLAType: string implements HasLabel
{
    case RESPONSE = 'response';
    case RESOLUTION = 'resolution';

    public function getLabel(): string|Htmlable|null
    {

        return match ($this) {
            self::RESPONSE => 'Response',
            self::RESOLUTION => 'Resolution',
        };
    }
}
