<x-filament-panels::page>
    <div class="lg:flex">
        <!-- Left Section for the Form -->
        <div class="w-full lg:w-1/2 p-4">
            <x-filament::section>
                <x-filament-panels::form wire:submit.prevent="save">
                    {{ $this->form }}
                    <x-filament-panels::form.actions
                        :actions="$this->getFormActions()"
                    />
                </x-filament-panels::form>
            </x-filament::section>
        </div>

        <!-- Tabs and Table Section -->
        <div class="w-full lg:w-1/2 p-4">
            <!-- Horizontally scrollable tabs -->
            <div class="flex space-x-4 border-b-2 overflow-x-auto whitespace-nowrap">
                @foreach ($this->getTabs() as $tabName => $tab)
                    <button
                        class="px-4 py-2 focus:outline-none {{ $selectedTab === $tabName ? 'border-b-2 border-blue-500' : '' }}"
                        wire:click.prevent="setActiveTab('{{ $tabName }}')">
                        {{ $tab->getLabel() }}
                        <span class="ml-2 bg-gray-200 text-xs rounded-full px-2 py-1">{{ $tab->getBadge() }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Table Rendering Based on Selected Tab -->
            <div class="mt-4">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
