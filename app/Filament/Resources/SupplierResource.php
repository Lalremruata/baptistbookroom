<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use App\Models\SupplierFinancials;
use App\Tables\Columns\SupplierBalance;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
// use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;


class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected ?string $title = 'Custom Page Heading';
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
                        ->numeric()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('opening_balance')
                        ->numeric(),
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
        ->header(view('tables.header.supplier-header'))
            ->columns([
                TextColumn::make('*')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('supplier_name')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                SupplierBalance::make('balance'),
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
                Tables\Actions\EditAction::make()
                ->iconButton(),
                DeleteAction::make()
                ->iconButton(),
                Tables\Actions\Action::make('supplier-details')
                ->url(fn (Supplier $record): string => static::getUrl('supplier-financial',['record' => $record])),
                ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->recordUrl(
                fn (Model $record): string => static::getUrl('supplier-financial',['record' => $record]),
            )
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
            'edit' => Pages\ManageSuppliers::route('/{record}/edit'),
            'supplier-financial' => Pages\SupplierDetail::route('/{record}/supplier-details'),

        ];
    }

}

