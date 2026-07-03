<?php

namespace App\Filament\Resources\EstimateResource\Pages;

use App\Filament\Resources\EstimateResource;
use App\Models\Branch;
use Filament\Resources\Pages\ListRecords;

class ListEstimates extends ListRecords
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
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
