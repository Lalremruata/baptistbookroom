<x-filament-panels::page>
<div class="lg:flex">
    <div class="w-full px-3 lg:w-2/3 lg:flex-none">
        {{ $this->table }}
    </div>
    <div class="px-3 lg:w-1/3 lg:flex-none">
        
            <div class="flex-auto">
            <div class="flex flex-col">
            <x-filament::section>
                <h5 class="mb-4 leading-normal font-bold">{{$this->record->supplier_name}}</h5>
                <span class="mb-2 leading-tight text-sm">A/C No.: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->account_number}}</span></span>
                <span class="mb-2 leading-tight text-sm">Ph. Number: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->contact_number}}</span></span>
                <span class="leading-tight text-sm">VAT Number: <span class="font-semibold text-slate-700 sm:ml-2">FRB1235476</span></span>
                </x-filament::section>
            </div>
            </div>       
    </div>
</div>
</x-filament-panels::page>
