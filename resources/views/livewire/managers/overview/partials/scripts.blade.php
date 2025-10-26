<!-- Chart Scripts -->
<script>
    // Ensure Chart.js is available before attempting to initialize charts.
    // If Chart is not yet loaded, dynamically load it and cache the loader promise on window.
    function ensureChartJsLoaded() {
        if (typeof Chart !== 'undefined') {
            return Promise.resolve();
        }

        if (window._chartJsLoader) {
            return window._chartJsLoader;
        }

        window._chartJsLoader = new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            s.async = true;
            s.onload = () => resolve();
            s.onerror = (e) => reject(e);
            document.head.appendChild(s);
        });

        return window._chartJsLoader;
    }

    // Initialize or re-initialize charts safely
    function initCharts() {
        // Revenue Chart
        const revenueEl = document.getElementById('revenueChart');
        if (revenueEl) {
            const revenueCtx = revenueEl.getContext('2d');
            // Destroy previous instance if exists to avoid duplicates
            if (window._revenueChart instanceof Chart) {
                window._revenueChart.destroy();
            }
            window._revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($monthlyRevenueChart)->pluck('month')),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: @json(collect($monthlyRevenueChart)->pluck('revenue')),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Contract Status Chart
        const contractEl = document.getElementById('contractChart');
        if (contractEl) {
            const contractCtx = contractEl.getContext('2d');
            if (window._contractChart instanceof Chart) {
                window._contractChart.destroy();
            }
            window._contractChart = new Chart(contractCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(collect($contractStatusChart)->pluck('status')),
                    datasets: [{
                        data: @json(collect($contractStatusChart)->pluck('count')),
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(239, 68, 68)',
                            'rgb(249, 115, 22)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }

    // Run once when initial DOM ready (wait for Chart.js)
    document.addEventListener('DOMContentLoaded', function() {
        ensureChartJsLoaded().then(initCharts).catch(() => {
            console.warn(
            'Chart.js failed to load, charts will not be initialized on DOMContentLoaded.');
        });
    });

    // Run after Livewire finishes initial load (hydrates) — covers the case where Livewire
    // morphs the DOM after DOMContentLoaded and causes canvases to be replaced.
    window.addEventListener('livewire:load', () => {
        ensureChartJsLoaded().then(initCharts).catch(() => {
            console.warn('Chart.js failed to load on livewire:load');
        });
    });

    // Also catch Livewire update events (server updates and re-renders)
    window.addEventListener('livewire:update', () => {
        ensureChartJsLoaded().then(initCharts).catch(() => {
            console.warn('Chart.js failed to load on livewire:update');
        });
    });

    // Also run after Livewire updates the DOM
    if (window.Livewire) {
        // message.processed runs after DOM morphing; re-initialize charts when our elements are present
        Livewire.hook('message.processed', (message, component) => {
            // If the response affected our component, re-init charts
            ensureChartJsLoaded().then(initCharts).catch(() => {});
        });

        // Keep existing custom event listener for explicit refresh
        Livewire.on('refresh-charts', () => {
            ensureChartJsLoaded().then(initCharts).catch(() => {});
        });
    }
</script>
