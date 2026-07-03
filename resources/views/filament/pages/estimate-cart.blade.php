<x-filament-panels::page>
    <div class="lg:flex">
        <div class="w-full lg:w-1/2 p-4">
            <x-filament::section>
                <x-filament-panels::form wire:submit="save">
                    {{ $this->form }}
                    <x-filament-panels::form.actions
                        :actions="$this->getFormActions()"
                    />
                </x-filament-panels::form>
            </x-filament::section>
        </div>
            <div class="w-full lg:w-1/2 p-4">
                {{ $this->table }}
            </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('focusItemSearch', () => {
                // Give Livewire a moment to re-render the reset form, then open
                // and focus the Item search so the next item can be typed/scanned.
                setTimeout(() => {
                    const wrap = document.getElementById('estimate-item-field');
                    if (!wrap) return;
                    const control = wrap.querySelector('button, [role="combobox"], input');
                    if (control) {
                        control.focus();
                        control.click();
                    }
                }, 150);
            });
        });
    </script>
</x-filament-panels::page>
