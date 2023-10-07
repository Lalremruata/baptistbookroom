<?php

namespace App\Filament\Resources\StockDistributeResource\Pages;

use App\Filament\Resources\StockDistributeResource;
use App\Models\StockDistribute;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockDistribute extends CreateRecord
{
    protected static string $resource = StockDistributeResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['user_id'] = auth()->id();
    //     return $data;
    // }
    public function submit(): void
    {

        // dd($this->form->getState());
        // create your logic to save to DB
    }
}
