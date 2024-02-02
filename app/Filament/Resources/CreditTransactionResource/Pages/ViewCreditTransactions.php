<?php

namespace App\Filament\Resources\CreditTransactionResource\Pages;

use App\Filament\Resources\CreditTransactionResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ViewCreditTransactions extends ViewRecord
{
    protected static string $resource = CreditTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Create')
            ->form([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                // ...
            ]),
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_name'),
                TextColumn::make('recieved_amount'),
                TextColumn::make('total_amount'),
                TextColumn::make('recovered_amount'),
            ]);
        }

}
