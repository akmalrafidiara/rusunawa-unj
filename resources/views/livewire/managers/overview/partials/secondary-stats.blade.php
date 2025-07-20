<!-- Secondary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Occupants -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Occupants</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                        <p>Current occupant status</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalOccupants }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Verified</span>
                    <span class="font-medium text-green-600">{{ $activeOccupants }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Pending</span>
                    <span class="font-medium text-yellow-600">{{ $pendingOccupants }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Reports</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                        <p>Complaints and reports</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalReports }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Pending</span>
                    <span class="font-medium text-red-600">{{ $pendingReports }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Resolved</span>
                    <span class="font-medium text-green-600">{{ $resolvedReports }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Maintenance</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                        <p>Maintenance activities</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $maintenanceUnits }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Units Under Maintenance</div>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Upcoming (7 days)</span>
                    <span class="font-medium text-blue-600">{{ $upcomingMaintenance }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Completed This Month</span>
                    <span class="font-medium text-green-600">{{ $completedMaintenanceThisMonth }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
