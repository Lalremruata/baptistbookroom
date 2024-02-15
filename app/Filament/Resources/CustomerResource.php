<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Customer Credit';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int$navigationSort = 3;
    public static function canCreate(): bool
    {
        return 0;
    }
    public static function shouldRegisterNavigation(): bool
    {
        $allowedRoles = ['Admin', 'Agent'];
        return in_array(auth()->user()->roles->first()->title, $allowedRoles);
    }
    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->user_type == '1') {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            // If user type is not '1'
            // Retrieve a customer based on the user's branch
            $customer = Sale::where('branch_id', auth()->user()->branch_id)->first();

            // Use the relationship to access the customer_id
            return parent::getEloquentQuery()->where('id', optional($customer)->customer_id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('address'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('transactions')
                // ->url(fn (Customer $record): string => static::getUrl('transactions',['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                fn (Model $record): string => static::getUrl('transactions',['record' => $record]),
            );
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'transactions' => Pages\CustomerCreditTransactions::route('/{record}/transactions'),

        ];
    }
}
