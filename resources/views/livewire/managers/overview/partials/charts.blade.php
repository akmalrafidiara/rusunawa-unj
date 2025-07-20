<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Monthly Revenue Chart -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Monthly Revenue Trend</h3>
            <div class="h-64">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Contract Status Chart -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Contract Status
                Distribution</h3>
            <div class="h-64">
                <canvas id="contractChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
