<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditTransactionResource\Pages;
use App\Filament\Resources\CreditTransactionResource\RelationManagers;
use App\Models\CreditTransaction;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditTransactionResource extends Resource
{
    protected static ?string $model = CreditTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function canCreate(): bool
    {
        return 0;
    }
    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->user_type == '1') {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            $customer = Sale::where('branch_id',auth()->user()->branch_id)->first();
                return parent::getEloquentQuery()->where('customer_id', $customer->customer_id);

        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('customer.customer_name'),
                TextInput::make('recieved_amount'),
                TextInput::make('total_amount'),
                TextInput::make('recovered_amount'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_name'),
                TextColumn::make('recieved_amount'),
                TextColumn::make('total_amount'),
                TextColumn::make('recovered_amount'),
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('transactions')
                ->url(fn (CreditTransaction $record): string => static::getUrl('transactions',['record' => $record])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditTransactions::route('/'),
            'edit' => Pages\EditCreditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewCreditTransactions::route('/{record}'),
            'transactions' => Pages\CustomerCreditTransactions::route('/{record}/transactions'),
        ];
    }
}
