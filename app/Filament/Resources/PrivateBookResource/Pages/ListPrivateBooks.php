<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\Sale;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListPrivateBooks extends ListRecords
{
    protected static string $resource = PrivateBookResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['mainStock.item'])
            ->addSelect([
                'private_books.*',
                'total_sale_quantity' => Sale::query()
                    ->selectRaw('COALESCE(SUM(sales.quantity), 0)')
                    ->join('branch_stocks', 'sales.branch_stock_id', '=', 'branch_stocks.id')
                    ->join('main_stocks', 'branch_stocks.main_stock_id', '=', 'main_stocks.id')
                    ->whereColumn('main_stocks.item_id', 'private_books.item_id'),
                'total_sale_amount' => Sale::query()
                    ->selectRaw('COALESCE(SUM(sales.total_amount), 0)')
                    ->join('branch_stocks', 'sales.branch_stock_id', '=', 'branch_stocks.id')
                    ->join('main_stocks', 'branch_stocks.main_stock_id', '=', 'main_stocks.id')
                    ->whereColumn('main_stocks.item_id', 'private_books.item_id'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('help')
            ->modalContent(function (): View {
                $record = "privateBook";
                return view('filament.pages.help', [
                    'record' => $record,
                ]);
            } 
            )
            ->icon('heroicon-m-question-mark-circle')
            // ->iconButton()
            ->slideOver()
            ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
            ->modalSubmitAction(false)
            ->modalWidth(MaxWidth::Medium)
            ->modalAlignment(Alignment::Center)
        ];
    }
}
