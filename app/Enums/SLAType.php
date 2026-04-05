<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SLAType: string implements HasLabel
{
    case RESPONSE = 'response';
    case RESOLUTION = 'resolution';

    public function getLabel(): string | \Illuminate\Contracts\Support\Htmlable | null
    {

        return match ($this) {
            self::RESPONSE => 'Response',
            self::RESOLUTION => 'Resolution',
        };
    }
}
