<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\PrivateBook;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Support\Htmlable;

class EditMainStock extends EditRecord
{
    protected static string $resource = MainStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): Array
    {
        // dd($this->getRecord()->barcode);
        $subCategoryName = Item::with('subCategory')
        ->where('barcode', $this->getRecord()->barcode)
        ->first();
        if($subCategoryName)
        {
            $data['sub_category_id'] = $subCategoryName->subCategory->subcategory_name;
        }
        return $data;
    }
    protected function beforeSave(): void
    {
        try {
            PrivateBook::where('main_stock_id', $this->data['id'])
                ->update(['quantity' => $this->data['quantity']]);
            BranchStock::where('main_stock_id', $this->data['id'])
                ->update(['cost_price' => $this->data['cost_price'], 'mrp' => $this->data['mrp']]);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the MainStock with the specified ID is not found
            // You can log an error, redirect the user, or take other appropriate actions.
        }
    }
    public function getTitle(): string | Htmlable
    {
        /** @var MainStock */
        $record = $this->getRecord();

        return $record->item->item_name;
    }
}
