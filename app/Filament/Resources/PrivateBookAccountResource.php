<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrivateBookAccountResource\Pages;
use App\Filament\Resources\PrivateBookAccountResource\RelationManagers;
use App\Models\PrivateBookAccount;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrivateBookAccountResource extends Resource
{
    protected static ?string $model = PrivateBookAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manage Private Books';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                Forms\Components\TextInput::make('private_book_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('return_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('return_date')
                    ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('private_book_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->date()
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
            'index' => Pages\ListPrivateBookAccounts::route('/'),
            'create' => Pages\CreatePrivateBookAccount::route('/create'),
            'edit' => Pages\EditPrivateBookAccount::route('/{record}/edit'),
        ];
    }    
}
