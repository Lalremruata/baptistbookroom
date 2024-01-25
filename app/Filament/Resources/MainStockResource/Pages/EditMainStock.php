<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use App\Models\Item;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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
        $data['sub_category_id'] = $subCategoryName->subCategory->subcategory_name;
        return $data;
    }
}
