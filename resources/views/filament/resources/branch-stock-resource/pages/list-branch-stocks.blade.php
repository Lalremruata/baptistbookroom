<x-filament-panels::page>
    @php
        $stats = $this->getCurrentBranchStats();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Branch</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['branch_name'] }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Items</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_quantity']) }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost Value</div>
            <div class="text-2xl font-bold text-yellow-600">₹{{ number_format($stats['total_cost_value'], 2) }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">MRP Value</div>
            <div class="text-2xl font-bold text-green-600">₹{{ number_format($stats['total_mrp_value'], 2) }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit</div>
            <div class="text-2xl font-bold {{ $stats['total_profit_value'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ₹{{ number_format($stats['total_profit_value'], 2) }}
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>