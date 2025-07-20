<div class="flex flex-col gap-6">
    {{-- 1. Toolbar Filter --}}
    @include('livewire.managers.oprations.income-reports.partials._filters')

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- 2. Ringkasan Pendapatan --}}
        <div class="w-full lg:w-1/3">
            @include('livewire.managers.oprations.income-reports.partials._summary')
        </div>

        {{-- 3. Grafik Pemasukan --}}
        <div class="w-full lg:w-2/3">
            @include('livewire.managers.oprations.income-reports.partials._chart')
        </div>
    </div>

    {{-- 4. Tabel Riwayat Tagihan --}}
    @include('livewire.managers.oprations.income-reports.partials._table')
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            let incomeChart;

            const initChart = (data) => {
                if (incomeChart) {
                    incomeChart.destroy();
                }
                if (!data || !data.labels || !data.datasets || data.labels.length === 0) {
                    return; // Jangan render chart jika tidak ada data
                }

                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                incomeChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pendapatan',
                            data: data.datasets[0].data,
                            backgroundColor: gradient,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) =>
                                        `Pendapatan: Rp${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) =>
                                        `Rp${new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value)}`
                                }
                            }
                        }
                    }
                });
            }

            initChart(@json($chartData));

            Livewire.on('updateChart', (event) => {
                initChart(event[0]);
            });
        });
    </script>
@endpush
