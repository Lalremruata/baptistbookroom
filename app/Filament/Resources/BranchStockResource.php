<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchStockResource\Pages;
use App\Filament\Resources\BranchStockResource\RelationManagers;
use App\Models\BranchStock;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Actions\HeaderActionsPosition;


class BranchStockResource extends Resource
{
    protected static ?string $model = BranchStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                Forms\Components\TextInput::make('discount')
                    ->required()
                    ->numeric(),
                    ])->compact()
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('mainStock.item.item_name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mrp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mainStock.barcode')
                    ->label('Bar code')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
            ])
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
                ExportAction::make()->exports([
                    ExcelExport::make()->fromTable(),
                    // ExcelExport::make('form')->fromForm(),
                    ])
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
            // 'edit' => Pages\EditBranchStock::route('/{record}/edit'),
        ];
    }
}
