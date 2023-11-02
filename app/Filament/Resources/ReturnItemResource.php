<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnItemResource\Pages;
use App\Filament\Resources\ReturnItemResource\RelationManagers;
use App\Models\BranchStock;
use App\Models\MainStock;
use App\Models\ReturnItem;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReturnItemResource extends Resource
{
    protected static ?string $model = ReturnItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('branch_stock_id')
                ->reactive()
                ->label('Item')
                ->options(function(){
                     return BranchStock::with(['mainStock' => function ($query) {
                        $query->select('item_id', 'id');
                    }])
                    ->whereHas('mainStock', function ($query) {
                    $query->where('branch_id', auth()->user()->branch_id);
                    })
                    ->get()
                    ->pluck('mainStock.item.item_name', 'id')
                    ->toArray();
                })
                ->searchable()
                ->dehydrated()
                ->required(),
                TextInput::make('quantity_returned')
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $branchStockId = $get('branch_stock_id');
                        if ($branchStockId) {
                            $result=BranchStock::where('id',$branchStockId)
                            ->where('branch_id',auth()->user()->branch_id)
                            ->pluck('quantity','id')->first();
                                return $result;

                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $branchStockId = $get('branch_stock_id');
                        if ($branchStockId) {
                                $result=BranchStock::where('id',$branchStockId)
                                ->where('branch_id',auth()->user()->branch_id)
                                ->pluck('quantity','id')->first();
                                 return 'quantity available: '.$result;
                        }
                            return null;
                    })
                        ->hintColor('danger')
                        ->required(),
                Forms\Components\TextInput::make('return_note')
                    ->maxLength(255),
                Hidden::make('branch_id')
                    ->default(auth()->user()->branch_id),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Hidden::make('return_date')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branchStock.mainStock.item.item_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_returned')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('return_note')
                    ->searchable(),
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
                ->form([
                    Toggle::make('is_approved'),
                ])
                ->iconButton()
                ->after(function (Model $record, array $data) {
                    $branchStock = BranchStock::where('id', $record['branch_stock_id'])->first();
                    // dd($branchStock->mainStock->barcode);
                    $branchStock->quantity -= $record['quantity_returned'];
                    $branchStock->update();
                    $mainStock = MainStock::where('id', $branchStock->mainStock->id)->first();
                    $mainStock->quantity += $record['quantity_returned'];
                    $mainStock->update();
                }),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageReturnItems::route('/'),
        ];
    }
}
