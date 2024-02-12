<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditPrivateBook extends EditRecord
{
    protected static string $resource = PrivateBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function beforeSave(): void
    {
        try {
            MainStock::findOrFail($this->data['main_stock_id'])
                ->update(['quantity' => $this->data['quantity']]);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the MainStock with the specified ID is not found
            // You can log an error, redirect the user, or take other appropriate actions.
        }
    }
}
