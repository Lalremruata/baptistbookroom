<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
enum Type: string implements HasLabel, HasColor
{
    case Credit = 'credit';
    case Debit = 'debit';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Credit => 'credit',
            self::Debit => 'debit',
        };
    }
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Credit => 'success',
            self::Debit => 'danger',
        };
    }
}
