<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrivateBookExporter;
use App\Filament\Resources\PrivateBookResource\Pages;
use App\Models\Item;
use App\Models\PrivateBook;
use App\Tables\Columns\TotalBookAmount;
use App\Tables\Columns\TotalBookSale;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class PrivateBookResource extends Resource
{
    protected static ?string $model = PrivateBook::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Private Books';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->roles->contains('title', 'Admin');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('barcode')
                        ->label('search barcode')
                        ->autofocus()
                        ->afterStateUpdated(function(callable $set,Get $get){
                            $barcode = $get('barcode');
                            $item = Item::where('barcode', $barcode)
                            ->first();
                            if($item)
                            {
                                $set('item_id', $item->id);
                            }

                        })
                        ->reactive()
                        // ->dehydrated(false)
                        ->live(),
                    Select::make('item_id')
                        ->label('search item')
                        ->reactive()
                        ->searchable()
                        ->options(Item::query()->pluck('item_name', 'id'))
                        ->afterStateUpdated(fn(callable $set,Get $get)=>$set('barcode',Item::query()
                        ->where('id', $get('item_id'))->pluck('barcode')->first())),
                ])->columns(3),
                Section::make('')
                ->schema([
                Forms\Components\TextInput::make('file_no')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cost_price')
                    ->hiddenOn('edit')
                    ->dehydrated(false)
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('mrp')
                    ->hiddenOn('edit')
                    ->dehydrated(false)
                    ->required()
                    ->numeric(),
                 Forms\Components\TextInput::make('batch')
                    ->hiddenOn('edit')
                    ->dehydrated(false)
                    ->required()
                    ->numeric(),
                ])->columns(2),
                Section::make('')
                    ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('receive_from')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('author')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->maxLength(255),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('*')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('mainStock.item.item_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('receive_from')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('author')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('file_no')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                // TextColumn::make('quantity')
                //     ->weight(FontWeight::Bold)
                //     ->sortable(),
                TextColumn::make('mainStock.mrp')
                    ->label('mrp')
                    ->weight(FontWeight::Bold),
                TotalBookSale::make('total_sale'),
                TotalBookAmount::make('total_sale_amount'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('book-account')
                ->url(fn (PrivateBook $record): string => static::getUrl('book-account',['record' => $record])),

            ])
            ->recordUrl(
                fn (Model $record): string => static::getUrl('book-account',['record' => $record])
            )
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PrivateBookExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                ], position: HeaderActionsPosition::Bottom)
            ->paginated([25, 50, 100, 'all']);
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
            'index' => Pages\ListPrivateBooks::route('/'),
            'create' => Pages\CreatePrivateBook::route('/create'),
            'edit' => Pages\EditPrivateBook::route('/{record}/edit'),
            'book-account' => Pages\BookAccount::route('/{record}/book-account'),

        ];
    }
}
