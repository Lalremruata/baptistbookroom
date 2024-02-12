<x-filament-panels::page>
    <x-filament::section>
    @php
        $value=App\Models\CreditTransaction::where('customer_id', $this->record->id)->first();
        $sum=App\Models\CreditTransaction::where('customer_id', $this->record->id)->sum('recovered_amount');
        $remainingCredit = $value->total_amount - $sum;
        @endphp

        <div class="bg-blue-500 text-blue text-center">
         <h1 class="text-2xl font-bold">Customer Name: {{ $this->record->customer_name }}</h1><br>
         </div>
         <h5 class="text-xl font-bold">Total Amount: {{ $value->total_amount }}</h5>
         <h5 class="text-xl font-bold">Initial Amount Received: {{ $value->received_amount }}</h5>
         <h5 class="text-xl font-bold">Balance: {{ $remainingCredit }}</h5>
         
        </div>
    </x-filament::section>
    <x-filament::section>
        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </x-filament-panels::form>
        </x-filament::section>
    {{ $this->table }}
</x-filament-panels::page>
