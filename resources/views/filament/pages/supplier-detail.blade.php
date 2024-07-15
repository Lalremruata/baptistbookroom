<x-filament-panels::page>
    <div class="lg:flex flex flex-row">
        <div class="px-3 lg:w-1/2">
            <x-filament::section>
                <h2 class="mb-2 leading-normal font-bold">{{$this->record->supplier_name}}</h2><hr>
                <div class="flex">
                    <span class="m-2 leading-tight text-sm block">A/C No.: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->account_number}}</span></span><hr>
                    <span class="m-2 leading-tight text-sm block">Bank: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->bank_name}}</span></span><hr>
                </div>
                <div class="flex">
                    <span class="m-2 leading-tight text-sm block">Branch: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->branch}}</span></span><hr>
                    <span class="m-2 leading-tight text-sm block">IFSC Code: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->ifsc_code}}</span><hr>
                </div>
                <div class="flex">
                    <span class="m-2 leading-tight text-sm block">Ph. Number: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->contact_number}}</span></span><hr>
                    <span class="m-2 leading-tight text-sm block">Address: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->address}}</span></span><hr>
                    <span class="m-2 leading-tight text-sm block">Email: <span class="font-semibold text-slate-700 dark:text-white sm:ml-2">{{$this->record->email}}</span></span><hr>
                </div>
            </x-filament::section>
        </div>
            <div class="lg:w-1/2">
                <x-filament::section>
                    <h2 class="font-bold">Balance: {{$balance}}</h2>
                </x-filament::section>
            </div>

    </div>
    <div class="lg:flex">
        <div class="w-full lg:w-1/2 p-4">
            <h2 class="text-2xl font-semibold mb-4">Credit Side</h2>
            <livewire:supplier-credit-table :supplierId="$record->id" />
        </div>
        <div class="w-full lg:w-1/2 p-4">
            <h2 class="text-2xl font-semibold mb-4">Debit Side</h2>
            <livewire:supplier-debit-table :supplierId="$record->id" />
        </div>

    </div>
<x-filament-actions::modals />
</x-filament-panels::page>
