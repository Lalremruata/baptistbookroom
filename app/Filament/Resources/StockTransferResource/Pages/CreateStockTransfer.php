<?php

namespace App\Filament\Resources\StockTransferResource\Pages;

use App\Filament\Resources\StockTransferResource;
use App\Models\StockTransfer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;
    public StockTransfer $stockTransfer;

    public function submit(): void
    {
        $formData = $this->form->getState();

        // foreach ($formData as $data) {
        //     $item_id = $data['item_id'];
        //     $quantity = $data['quantity'];
        //     $transfer_date = $data['transfer_date'];
        //     $notes = $data['notes'];
        //     $branch_id = $data['branch_id'];

        // StockTransfer::create([
        //     'item_id' => $item_id,
        //     'quantity' => $quantity,
        //     'transfer_date' => $transfer_date,
        //     'notes' => $notes,
        //     'branch_id' => $branch_id,
        // ]);
        // }

        dd($this->form->getState());
        // create your logic to save to DB
    }
}
