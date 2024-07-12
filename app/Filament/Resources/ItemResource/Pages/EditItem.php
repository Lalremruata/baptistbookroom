<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use App\Models\BranchStock;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        try {
            MainStock::findOrFail($this->data['id'])
                ->update(['barcode' => $this->data['barcode']]);
            BranchStock::whereHas('mainStock', function ($query) {
                    $query->where('item_id', $this->data['id']);
                })->update(['barcode' => $this->data['barcode']]);

        } catch (ModelNotFoundException $e) {
            // Handle the case where the MainStock with the specified ID is not found
            // You can log an error, redirect the user, or take other appropriate actions.
        }
    }
}
