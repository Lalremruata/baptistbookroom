<?php

namespace App\Filament\Resources\CreditTransactionResource\Pages;

use App\Filament\Resources\CreditTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditTransactions extends ListRecords
{
    protected static string $resource = CreditTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
