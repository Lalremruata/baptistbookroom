<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Split;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('supplier_name')
                        ->autofocus()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                ])->columns(2)->compact()
                ,
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('account_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('ifsc_code')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('bank_name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('branch')
                        ->maxLength(255),
                ])->columns(2)->compact()
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('supplier-details')
                ->url(fn (Supplier $record): string => static::getUrl('supplier-financial',['record' => $record])),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
            'supplier-financial' => Pages\SupplierDetail::route('/{record}/supplier-details'),

        ];
    }

}

