<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrivateBook extends EditRecord
{
    protected static string $resource = PrivateBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
