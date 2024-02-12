<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Branch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        if(auth()->user()->user_type=='1') {
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
