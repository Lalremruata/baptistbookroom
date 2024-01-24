<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            // ->mutateFormDataUsing(function (array $data): array {
            //     // $data['current_balance'] = $data['initial_balance'];    
            //     // return $data;
            // }),
        ];
    }
    
}
