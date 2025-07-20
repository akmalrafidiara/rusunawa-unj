<!-- Key Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Units Overview -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <flux:icon.building-office-2 class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total Units
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $totalUnits }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <span class="font-medium text-green-600">{{ $occupiedUnits }} Occupied</span>
                <span class="text-gray-500 dark:text-gray-400 mx-2">•</span>
                <span class="font-medium text-blue-600">{{ $availableUnits }} Available</span>
            </div>
        </div>
    </div>

    <!-- Occupancy Rate -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <flux:icon.chart-bar class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Occupancy Rate
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $occupancyRate }}%
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $occupancyRate }}%"></div>
            </div>
        </div>
    </div>

    <!-- Active Contracts -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <flux:icon.document-text class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Active Contracts
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $activeContracts }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <span class="font-medium text-orange-600">{{ $expiringContractsThisMonth }} Expiring This
                    Month</span>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <flux:icon.currency-dollar class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Monthly Revenue
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <span class="font-medium text-red-600">{{ $overdueInvoices }} Overdue</span>
                <span class="text-gray-500 dark:text-gray-400 mx-2">•</span>
                <span class="font-medium text-green-600">{{ $paidInvoices }} Paid</span>
            </div>
        </div>
    </div>
</div>
