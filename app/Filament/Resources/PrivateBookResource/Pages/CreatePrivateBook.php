<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePrivateBook extends CreateRecord
{
    protected static string $resource = PrivateBookResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function beforeCreate(): void
    {
        $mainStock = new MainStock;
        $mainStock->cost_price = $this->data['cost_price'];
        $mainStock->mrp = $this->data['mrp'];
        $mainStock->batch = $this->data['batch'];
        $mainStock->item_id = $this->data['item_id'];
        $mainStock->quantity = $this->data['quantity'];
        $mainStock->barcode = $this->data['barcode'];
        $mainStock->save();
    }
    protected function handleRecordCreation(array $data): Model
    {
        $mainStockId = MainStock::latest()->pluck('id')->first();
        $newData = [
            'main_stock_id'=> $mainStockId,
        ];
        $data += $newData;
        return static::getModel()::create($data);
    }
}
