<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\MainStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrivateBook extends CreateRecord
{
    protected static string $resource = PrivateBookResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function beforeCreate(): void
    {
        // Runs after the form fields are saved to the database.
        $mainStock = new MainStock;
        $mainStock->cost_price = $this->data['cost_price'];
        $mainStock->mrp = $this->data['mrp'];
        $mainStock->batch = $this->data['batch'];
        $mainStock->item_id = $this->data['item_id'];
        $mainStock->quantity = $this->data['quantity'];
        $mainStock->save();
    }
}
