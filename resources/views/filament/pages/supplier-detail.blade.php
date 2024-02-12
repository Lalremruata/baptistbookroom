<x-filament-panels::page>
<div class="lg:flex">
    <div class="px-3 lg:w-1/3 lg:flex-none">

        <div class="flex-auto">
            <div class="flex flex-col">
            <x-filament::section>
                <h5 class="mb-2 leading-normal font-bold">{{$this->record->supplier_name}}</h5><hr>
                <span class="mb-2 mt-2 leading-tight text-sm block">A/C No.: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->account_number}}</span></span><hr>
                <span class="mb-2 mt-2 leading-tight text-sm block">Bank: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->bank_name}}</span></span><hr>
                <span class="leading-tight text-sm mt-2">Branch: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->branch}}</span><hr>
                <span class="leading-tight text-sm mt-2">IFSC Code: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->ifsc_code}}</span><hr>
                <span class="mb-2 leading-tight text-sm block mt-2">Ph. Number: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->contact_number}}</span></span><hr>
                <span class="mb-2 leading-tight text-sm block mt-2">Address: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->address}}</span></span><hr>
                <span class="mb-2 leading-tight text-sm block mt-2">Email: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->email}}</span></span><hr>

            </x-filament::section>
            </div>
        </div>
</div>
    <div class="w-full px-3 lg:w-2/3 lg:flex-none">
        {{ $this->table }}
    </div>
</div>
<x-filament-actions::modals />
</x-filament-panels::page>
