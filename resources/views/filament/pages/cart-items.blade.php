<x-filament-panels::page>
    <div class="lg:flex">
        <div class="w-full lg:w-1/2 p-4">
            <x-filament::section>
                <x-filament-panels::form wire:submit="save">
                    {{-- <x-filament-panels::form wire:submit="save"> --}}
                    {{ $this->form }}
                    <x-filament-panels::form.actions
                        :actions="$this->getFormActions()"
                    />
                </x-filament-panels::form>
            </x-filament::section>
        </div>
        {{-- <div>
            {{ $this->deleteAction }}
            <x-filament-actions::modals />
        </div> --}}
            <div class="w-full lg:w-1/2 p-4">{{ $this->table }}</div>
    </div>
</x-filament-panels::page>
