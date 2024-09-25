<?php

namespace App\Observers;

use App\Filament\Resources\BranchStockResource;
use App\Filament\Resources\StockDistributeResource;
use App\Models\StockDistribute;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class StockDistributeObserver
{
    /**
     * Handle the StockDistribute "created" event.
     */
    public function created(StockDistribute $stockDistribute): void
    {
        $users = User::where('branch_id', $stockDistribute->branch_id)->get();
        Notification::make()
        ->title('Stock Received Successfully')
        ->success()
        ->body("**{$stockDistribute->mainStock->item->item_name}**")
        ->actions([
            Action::make('view')
                ->button()
                ->markAsRead()
                ->url(StockDistributeResource::getUrl('view', ['record' => $stockDistribute->id]))
        ])
        ->sendToDatabase($users);
    }

    /**
     * Handle the StockDistribute "updated" event.
     */
    public function updated(StockDistribute $stockDistribute): void
    {
        $users = User::where('branch_id', $stockDistribute->branch_id)->get();
        Notification::make()
        ->title('Stock Received Successfully')
        ->success()
        ->body("**{$stockDistribute->mainStock->item->item_name}**")
        ->actions([
            Action::make('view')
                ->button()
                ->markAsRead()
                ->url(BranchStockResource::getUrl('view'))
        ])
        ->sendToDatabase($users);
    }

    /**
     * Handle the StockDistribute "deleted" event.
     */
    public function deleted(StockDistribute $stockDistribute): void
    {
        //
    }

    /**
     * Handle the StockDistribute "restored" event.
     */
    public function restored(StockDistribute $stockDistribute): void
    {
        //
    }

    /**
     * Handle the StockDistribute "force deleted" event.
     */
    public function forceDeleted(StockDistribute $stockDistribute): void
    {
        //
    }
}
