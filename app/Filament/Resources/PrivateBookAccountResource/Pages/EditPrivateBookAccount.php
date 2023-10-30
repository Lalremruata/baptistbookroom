<?php

namespace App\Filament\Resources\PrivateBookAccountResource\Pages;

use App\Filament\Resources\PrivateBookAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrivateBookAccount extends EditRecord
{
    protected static string $resource = PrivateBookAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
