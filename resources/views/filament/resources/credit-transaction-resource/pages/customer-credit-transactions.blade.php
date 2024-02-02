<x-filament-panels::page>
    <x-filament::section>
                <x-filament-panels::form wire:submit="save">
                    {{-- <x-filament-panels::form wire:submit="save"> --}}
                    {{ $this->form }}
                    <x-filament-panels::form.actions
                        :actions="$this->getFormActions()"
                    />
                </x-filament-panels::form>
            </x-filament::section>
    {{ $this->table }}
</x-filament-panels::page>
