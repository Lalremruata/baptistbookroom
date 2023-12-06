<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Supplier;
use Filament\Actions\Contracts\HasRecord;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
// use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class SupplierDetail extends Page implements HasRecord
{
    // protected static string $resource = SupplierResource::class;
    // public SupplierResource $supplierResource;

    // public ?array $data = [];

    protected static string $view = 'filament.resources.supplier-resource.pages.supplier-detail';
   
    use InteractsWithRecord;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
 
        static::authorizeResourceAccess();
    }
    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->query(Supplier::query()->where('id', $this->getRecord()))
    //         ->columns([
    //             TextColumn::make('supplier_name'),
    //             TextColumn::make('contact_number'),
    //             TextColumn::make('email'),
    //             TextColumn::make('address')
    //         ]);
    // }
}
