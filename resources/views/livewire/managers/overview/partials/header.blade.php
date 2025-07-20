<!-- Header -->
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Welcome to Rusunawa UNJ Management System</p>
    </div>
    <button wire:click="refreshData"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
            </path>
        </svg>
        Refresh Data
    </button>
</div>
