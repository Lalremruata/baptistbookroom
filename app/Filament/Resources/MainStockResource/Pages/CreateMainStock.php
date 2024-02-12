<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMainStock extends CreateRecord
{
    protected static string $resource = MainStockResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function handleRecordCreation(array $data): Model
    {
        // Runs after the form fields are saved to the database.
        $mainStock = MainStock::where('item_id',$this->data['item_id'])->first();
        if($mainStock)
        {
            $mainStock->cost_price = $this->data['cost_price'];
            $mainStock->mrp = $this->data['mrp'];
            $mainStock->batch = $this->data['batch'];
            $mainStock->quantity += $this->data['quantity'];
            $mainStock->barcode = $this->data['barcode'];
            $mainStock->update();
            return $mainStock;
        }
        else
            return static::getModel()::create($data);

    }
}
