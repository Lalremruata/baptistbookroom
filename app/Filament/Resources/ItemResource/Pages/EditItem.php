<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use App\Models\BranchStock;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

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
            MainStock::where('item_id',$this->data['id'])
                ->update(['barcode' => $this->data['barcode']]);
            BranchStock::whereHas('mainStock', function ($query) {
                    $query->where('item_id', $this->data['id']);
                })->update(['barcode' => $this->data['barcode']]);

        } catch (ModelNotFoundException $e) {
        // Log error details
        Log::error('MainStock not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Log any other exceptions
            Log::error('Error updating barcode: ' . $e->getMessage());
        }
    }
}
