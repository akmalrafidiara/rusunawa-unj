<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
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

        // Contract Status Chart
        const contractCtx = document.getElementById('contractChart').getContext('2d');
        const contractChart = new Chart(contractCtx, {
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

        // Listen for refresh event
        Livewire.on('refresh-charts', () => {
            revenueChart.update();
            contractChart.update();
        });
    });
</script>
