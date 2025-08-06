<?php

namespace App\Filament\Resources\BranchStockResource\Widgets;

use App\Models\BranchStock;
use App\Models\Branch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

class BranchStockStats extends BaseWidget
{
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = null;
    
    public ?int $currentBranchId = null;

    // Listen for the branch-changed event
    #[On('branch-changed')]
    public function updateBranch($branchId): void
    {
        $this->currentBranchId = $branchId;
    }

    protected function getStats(): array
    {
        // Check if user is Admin/Manager
        $allowedRoles = ['Admin', 'Manager'];
        $isAdminOrManager = in_array(auth()->user()->roles->first()->title, $allowedRoles);
        
        // For branch users, always use their branch_id
        if (!$isAdminOrManager) {
            $currentBranchId = auth()->user()->branch_id;
            $query = BranchStock::query()->where('branch_id', $currentBranchId);
        } else {
            // For Admin/Manager, use the selected tab or all branches
            $currentBranchId = $this->currentBranchId ?? $this->getCurrentBranchId();
            $query = BranchStock::query();
            
            // Apply branch filter if specific branch is selected
            if ($currentBranchId) {
                $query->where('branch_id', $currentBranchId);
            }
        }
        
        $branchStocks = $query->get();
        
        // Calculate values
        $totalCostValue = $branchStocks->sum(function($stock) {
            return ($stock->cost_price ?? 0) * ($stock->quantity ?? 0);
        });
        
        $totalMrpValue = $branchStocks->sum(function($stock) {
            return ($stock->mrp ?? 0) * ($stock->quantity ?? 0);
        });
        
        $totalProfitValue = $totalMrpValue - $totalCostValue;
        $totalQuantity = $branchStocks->sum('quantity') ?? 0;
        
        // Get branch name - for branch users, always show their branch
        if (!$isAdminOrManager && auth()->user()->branch_id) {
            $branchName = Branch::find(auth()->user()->branch_id)?->branch_name ?? 'My Branch';
        } else {
            $branchName = $currentBranchId 
                ? (Branch::find($currentBranchId)?->branch_name ?? 'Unknown Branch')
                : 'All Branches';
        }

        return [
            Stat::make('Branch', $branchName)
                ->description('Current selection')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('gray'),
                
            Stat::make('Items', Number::format($totalQuantity))
                ->description('Total quantity')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
                
            Stat::make('Cost Value', '₹' . Number::format($totalCostValue, 2))
                ->description('Total investment')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
                
            Stat::make('MRP Value', '₹' . Number::format($totalMrpValue, 2))
                ->description('Potential revenue')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),
                
            Stat::make('Profit', '₹' . Number::format($totalProfitValue, 2))
                ->description($totalProfitValue >= 0 ? 'Expected profit' : 'Loss')
                ->descriptionIcon($totalProfitValue >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($totalProfitValue >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getCurrentBranchId(): ?int
    {
        // Check if user is a branch user first
        $allowedRoles = ['Admin', 'Manager'];
        if (!in_array(auth()->user()->roles->first()->title, $allowedRoles)) {
            return auth()->user()->branch_id;
        }
        
        // For Admin/Manager, get from active tab
        $activeTab = request()->get('activeTab', 'all');
        
        if ($activeTab === 'all' || empty($activeTab)) {
            return null;
        }
        
        $branch = Branch::where('branch_name', $activeTab)->first();
        return $branch?->id;
    }
}