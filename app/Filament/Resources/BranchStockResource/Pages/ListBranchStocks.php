<?php

namespace App\Filament\Resources\BranchStockResource\Pages;

use App\Filament\Resources\BranchStockResource;
use App\Models\Branch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;

class ListBranchStocks extends ListRecords
{
    protected static string $resource = BranchStockResource::class;

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
    public function getTabs(): array
    {
        $allowedRoles = ['Admin', 'Manager'];
        if(in_array(auth()->user()->roles->first()->title, $allowedRoles)) {
            $branches = Branch::all();
            $tabs=[null => ListRecords\Tab::make('All'),];
            foreach ($branches as $branch) {
                $tabs[$branch->branch_name] = ListRecords\Tab::make()
            ->query(fn ($query) => $query->where('branch_id', $branch->id));
            }
            return $tabs;
        }
        else {
        return [
            //return nothing
        ];
        }

    }
}
