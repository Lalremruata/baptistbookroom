<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BranchStockExporter;
use App\Filament\Resources\BranchStockResource\Pages;
use App\Models\BranchStock;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Exports\Enums\ExportFormat;

class BranchStockResource extends Resource
{
    protected static ?string $model = BranchStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    // protected static ?string $navigationParentItem = 'Distribute Report';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'Branch Stock Report';
    protected static ?int $navigationSort = 4;
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
                Section::make()
                ->schema([
                Forms\Components\Select::make('branch_id')
                    ->relationship('branch','branch_name')
                    ->required(),
                Forms\Components\Select::make('item_id')
                    ->searchable()
                    ->required()
                    ->relationship('items','item_name'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cost_price')
                    ->required()
                    ->numeric(),
                // Forms\Components\TextInput::make('discount')
                //     ->required()
                //     ->numeric(),
                    ])->compact()
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('*')
                ->weight(FontWeight::Bold)
                ->rowIndex(),
                TextColumn::make('branch.branch_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('mainStock.item.item_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('quantity')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->summarize(Sum::make()->label('Total'))
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mrp')
                    ->summarize(Sum::make()->label('Total'))
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mainStock.barcode')
                    ->label('Bar code')
                    ->searchable()
                    ->sortable(),
            ])->searchDebounce('750ms')
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                        }),
                    SelectFilter::make('branch')
                        ->relationship('branch','branch_name')
                        ->hidden(! auth()->user()->user_type=='1')
                ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
            ->headerActions([
                ExportAction::make()
                    ->exporter(BranchStockExporter::class)
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
            'index' => Pages\ListBranchStocks::route('/'),
            'create' => Pages\CreateBranchStock::route('/create'),
            'view' => Pages\ViewBranchStock::route('/{record}'),
            'edit' => Pages\EditBranchStock::route('/{record}/edit'),
        ];
    }
}
