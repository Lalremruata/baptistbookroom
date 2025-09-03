<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnItemResource\Pages;
use App\Models\BranchStock;
use App\Models\Item;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnItemResource extends Resource
{
    protected static ?string $model = ReturnItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'Branch Return Item';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('barcode')
                    ->label('Barcode Search')
                    ->autofocus()
                    ->afterStateUpdated(function(callable $set, Get $get) {
                        $barcode = $get('barcode');

                        // Fetch all branch stock records with the same barcode and belonging to the current branch
                        $branchStocks = BranchStock::with('mainStock.item')
                            ->where('barcode', $barcode)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->get();

                        if ($branchStocks->count() === 1) {
                            // If only one item matches the barcode
                            $branchStock = $branchStocks->first();
                            $set('branch_stock_id', $branchStock->id);
                            $set('item_id', $branchStock->mainStock->item->item_name); // or use item_info if available
                        } elseif ($branchStocks->count() > 1) {
                            // If multiple items have the same barcode, clear and allow item selection
                            $set('branch_stock_id', null);
                            $set('item_id', null);
                        } else {
                            // If no match is found, clear the selection
                            $set('branch_stock_id', null);
                            $set('item_id', null);
                        }
                    })
                    ->reactive()
                    ->live()
                    ->extraInputAttributes(['onkeydown' => 'if(event.key === "Enter") { event.preventDefault(); }']),

                Select::make('item_id')
                    ->reactive()
                    ->label('Item Search')
                    ->options(function (callable $get) {
                        $barcode = $get('barcode');
                        // Fetch all branch stock records with the same barcode and belonging to the current branch
                        $branchStocks = BranchStock::with('mainStock.item')
                            ->where('branch_id', auth()->user()->branch_id)
                            ->when($barcode, function($query) use ($barcode) {
                                return $query->where('barcode', $barcode);
                            })
                            ->get();

                        // Display items with item_info or item_name
                        return $branchStocks->pluck('mainStock.item.item_name', 'id')->toArray(); // Change to item_info if available
                    })
                    ->afterStateUpdated(function (callable $set, Get $get) {
                        $branchStockId = $get('item_id');
                        $branchStock = BranchStock::with('mainStock')->find($branchStockId);

                        if ($branchStock) {
                            $set('barcode', $branchStock->barcode);
                            $set('branch_stock_id', $branchStock->id);
                        } else {
                            // Clear the barcode if no item is selected
                            $set('barcode', null);
                        }
                    })
                    ->noSearchResultsMessage('No items found.')
                    ->searchingMessage('Searching items')
                    ->searchable()
                    ->dehydrated(false)
                    ->required()
                    ->live(),

                TextInput::make('quantity_returned')
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $branchStockId = $get('branch_stock_id');
                        if ($branchStockId) {
                            $result = BranchStock::where('id', $branchStockId)
                                ->where('branch_id', auth()->user()->branch_id)
                                ->pluck('quantity', 'id')->first();
                            return $result;
                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $branchStockId = $get('branch_stock_id');
                        $barcode = $get('barcode');
                        if ($branchStockId) {
                            $result = BranchStock::where('id', $branchStockId)
                                ->where('branch_id', auth()->user()->branch_id)
                                ->pluck('quantity', 'id')->first();
                            if($result)
                                return 'quantity available: '.$result;
                            else
                                return 'stock unavailable';
                        }
                        elseif ($barcode) {
                            $result = BranchStock::where('barcode', $barcode)
                                ->where('branch_id', auth()->user()->branch_id)
                                ->pluck('quantity', 'id')->first();
                            if($result)
                                return 'quantity available: '.$result;
                            else
                                return 'stock unavailable';
                        }
                        return null;
                    })
                    ->hintColor('danger')
                    ->required()
                    ->hidden(function (Get $get): bool {
                        if(BranchStock::where('barcode', $get('barcode'))->first() || $get('branch_stock_id'))
                            return false;
                        else
                            return true;
                    }),

                Forms\Components\TextInput::make('return_note')
                    ->maxLength(255),

                Hidden::make('branch_stock_id'),
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
                Tables\Columns\TextColumn::make('branchStock.item.item_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branchStock.mainStock.barcode')
                    ->label('Barcode')
                    ->searchable(isIndividual: true)
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            // Make rows clickable to trigger the approval action
            ->recordAction('approveReturn')
            ->recordUrl(null) // Disable default record URL
            ->actions([
                // Fixed: Capital 'A' in Action
                Tables\Actions\Action::make('approveReturn')
                    ->label('Approve Return')
                    ->form([
                       Section::make('Return Details')
                           ->schema([
                               Placeholder::make('item_info')
                                   ->label('Item Information')
                                   ->content(function (Model $record): string {
                                       $item = $record->branchStock->item ?? null;
                                       $barcode = $record->branchStock->mainStock->barcode ?? 'N/A';

                                       if (!$item) {
                                           return "Barcode: {$barcode}";
                                       }

                                       return "{$item->item_name} (Barcode: {$barcode})";
                                   }),

                               Placeholder::make('return_info')
                                   ->label('Return Information')
                                   ->content(function (Model $record): string {
                                       $returnDate = 'Not set';

                                       if ($record->return_date) {
                                           if ($record->return_date instanceof \Carbon\Carbon) {
                                               $returnDate = $record->return_date->format('M d, Y');
                                           } else {
                                               try {
                                                   $returnDate = \Carbon\Carbon::parse($record->return_date)->format('M d, Y');
                                               } catch (\Exception $e) {
                                                   $returnDate = $record->return_date;
                                               }
                                           }
                                       }

                                       return "Quantity: {$record->quantity_returned} | Date: {$returnDate} | Note: " . ($record->return_note ?: 'None');
                                   }),

                               TextInput::make('barcode')
                                   ->label('Barcode')
                                   ->default(function (Model $record) {
                                       return $record->branchStock->mainStock->barcode ?? 'N/A';
                                   })
                                   ->disabled()
                                   ->dehydrated(false),
                           ]),

                        Section::make('Approval')
                            ->schema([
                                Toggle::make('is_approved')
                                    ->label('Approve Return')
                                    ->helperText('This will update stock quantities')
                                    // Fixed: Set the current state as default
                                    ->default(function (Model $record): bool {
                                        return (bool) $record->is_approved;
                                    })
                                    ->inline(false),
                            ]),
                    ])
                    ->modalWidth('md')
                    // Fixed: Use action() instead of after() for better control
                    ->action(function (Model $record, array $data) {
                        Log::info('Approve action triggered', [
                            'record_id' => $record->id,
                            'old_approved' => $record->is_approved,
                            'new_approved' => $data['is_approved'],
                            'quantity_returned' => $record->quantity_returned
                        ]);

                        try {
                            $oldApprovalStatus = (bool) $record->is_approved;
                            $newApprovalStatus = (bool) $data['is_approved'];

                            // Update the record first
                            $record->update(['is_approved' => $newApprovalStatus]);

                            // Process approval change
                            if ($newApprovalStatus && !$oldApprovalStatus) {
                                // Being approved for the first time
                                $branchStock = BranchStock::find($record->branch_stock_id);

                                if (!$branchStock) {
                                    throw new \Exception('Branch stock not found');
                                }

                                // Check if there's enough stock to return
                                if ($branchStock->quantity < $record->quantity_returned) {
                                    throw new \Exception("Insufficient stock for return. Available: {$branchStock->quantity}, Required: {$record->quantity_returned}");
                                }

                                // Perform atomic operations
                                DB::transaction(function () use ($branchStock, $record) {
                                    Log::info('Processing approval - decrementing branch stock', [
                                        'branch_stock_id' => $branchStock->id,
                                        'current_quantity' => $branchStock->quantity,
                                        'decrement_by' => $record->quantity_returned
                                    ]);

                                    // Decrement branch stock
                                    $branchStock->decrement('quantity', $record->quantity_returned);

                                    Log::info('Processing approval - incrementing main stock', [
                                        'main_stock_id' => $branchStock->mainStock->id,
                                        'current_quantity' => $branchStock->mainStock->quantity,
                                        'increment_by' => $record->quantity_returned
                                    ]);

                                    // Increment main stock
                                    $branchStock->mainStock->increment('quantity', $record->quantity_returned);
                                });

                                Notification::make()
                                    ->title('Return Approved Successfully')
                                    ->body("Stock quantities updated: Branch stock decreased by {$record->quantity_returned}, Main stock increased by {$record->quantity_returned}")
                                    ->success()
                                    ->send();

                            } elseif (!$newApprovalStatus && $oldApprovalStatus) {
                                // Being unapproved - reverse the operation
                                $branchStock = BranchStock::find($record->branch_stock_id);

                                if ($branchStock) {
                                    DB::transaction(function () use ($branchStock, $record) {
                                        Log::info('Processing unapproval - reversing stock changes');

                                        // Reverse the operations
                                        $branchStock->increment('quantity', $record->quantity_returned);
                                        $branchStock->mainStock->decrement('quantity', $record->quantity_returned);
                                    });

                                    Notification::make()
                                        ->title('Return Unapproved')
                                        ->body("Stock quantities reversed: Branch stock increased by {$record->quantity_returned}, Main stock decreased by {$record->quantity_returned}")
                                        ->warning()
                                        ->send();
                                }
                            } else {
                                // No approval status change
                                Notification::make()
                                    ->title('Return Updated')
                                    ->body('Return record updated successfully')
                                    ->success()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Log::error('Error processing return approval: ' . $e->getMessage(), [
                                'record_id' => $record->id,
                                'trace' => $e->getTraceAsString()
                            ]);

                            // Revert the record update if stock update failed
                            $record->update(['is_approved' => $record->getOriginal('is_approved')]);

                            Notification::make()
                                ->title('Error Processing Return')
                                ->body('Failed to update stock quantities: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            throw $e; // Re-throw to prevent action completion
                        }
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color(function (Model $record): string {
                        return $record->is_approved ? 'success' : 'warning';
                    })
                    ->iconButton(),

                // Optional: Add a separate view action for read-only details
                Tables\Actions\Action::make('viewDetails')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('md')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->form([
                        Section::make('Return Details')
                            ->schema([
                                Placeholder::make('item_info')
                                    ->label('Item Information')
                                    ->content(function (Model $record): string {
                                        $item = $record->branchStock->item ?? null;
                                        $barcode = $record->branchStock->mainStock->barcode ?? 'N/A';
                                        return $item ? "{$item->item_name} (Barcode: {$barcode})" : "Barcode: {$barcode}";
                                    }),

                                Placeholder::make('return_details')
                                    ->label('Return Information')
                                    ->content(function (Model $record): string {
                                        $returnDate = $record->return_date ?
                                            (\Carbon\Carbon::parse($record->return_date)->format('M d, Y')) : 'Not set';
                                        $approved = $record->is_approved ? 'Yes' : 'No';
                                        $branch = $record->branch->branch_name ?? 'Unknown';
                                        $user = $record->user->name ?? 'Unknown';

                                        return "Quantity: {$record->quantity_returned}\nDate: {$returnDate}\nApproved: {$approved}\nBranch: {$branch}\nUser: {$user}\nNote: " . ($record->return_note ?: 'None');
                                    }),
                            ])
                    ])
                    ->iconButton(),
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
