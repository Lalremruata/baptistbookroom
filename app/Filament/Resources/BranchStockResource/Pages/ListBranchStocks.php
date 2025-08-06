<?php

namespace App\Filament\Resources\BranchStockResource\Pages;

use App\Filament\Resources\BranchStockResource;
use App\Models\Branch;
use App\Models\BranchStock;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;

class ListBranchStocks extends ListRecords
{
    protected static string $resource = BranchStockResource::class;

    // Add this property to track current branch
    public ?int $currentBranchId = null;

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
                })
                ->icon('heroicon-m-question-mark-circle')
                ->slideOver()
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->modalSubmitAction(false)
                ->modalWidth(MaxWidth::Medium)
                ->modalAlignment(Alignment::Center)
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\BranchStockResource\Widgets\BranchStockStats::class,
        ];
    }

    // Override this method to detect tab changes
    public function updatedActiveTab(): void
    {
        $this->currentBranchId = $this->getCurrentBranchId();
        
        // Emit event to refresh widgets
        $this->dispatch('branch-changed', branchId: $this->currentBranchId);
    }

    public function getCurrentBranchId(): ?int
    {
        $activeTab = $this->activeTab ?? 'all';
        
        if ($activeTab === 'all' || empty($activeTab)) {
            return null;
        }
        
        $branch = Branch::where('branch_name', $activeTab)->first();
        return $branch?->id;
    }

    public function getTabs(): array
    {
        $allowedRoles = ['Admin', 'Manager'];
        if(in_array(auth()->user()->roles->first()->title, $allowedRoles)) {
            $branches = Branch::all();
            $tabs = ['all' => ListRecords\Tab::make('All')];
            
            foreach ($branches as $branch) {
                $tabs[$branch->branch_name] = ListRecords\Tab::make()
                    ->query(fn ($query) => $query->where('branch_id', $branch->id));
            }
            return $tabs;
        } else {
            return [];
        }
    }
}