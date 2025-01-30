<?php

namespace App\Filament\Resources\StockDistributeResource\Pages;

use App\Filament\Resources\StockDistributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;

class ListStockDistribute extends ListRecords
{
    protected static string $resource = StockDistributeResource::class;
    // public function mount(): void
    // {
    //     abort_unless(auth()->user()->user_type=='1', 403);
    // }
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
