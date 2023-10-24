<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
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
            return parent::getEloquentQuery()->where('branch_id', auth()->user()->branch_id);

        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->schema([
                    Section::make()
                        ->schema([
                            Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required(),
                        ])->columns(2),

                    Section::make()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                        ->relationship('item', 'item_name')
                        ->required(),
                        Forms\Components\DatePicker::make('sale_date'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric(),
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('selling_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('discount')
                            ->numeric(),
                    ])->columns(2)
                ])->columnSpan(['lg' => 3]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                ->label('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make('sales_date'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['sales_date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '=', $date),
                        );
                })
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
