@extends('layouts.layout')

@section('content')
    <div class="space-y-10 font-serif">

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl p-6 shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm uppercase tracking-wide">Alumni Forms Submission</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['alumni_forms']) }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm uppercase tracking-wide">Seats Booked</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['bookings']) }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm uppercase tracking-wide">Teacher Jobs Submission</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['job_posts']) }}</p>
            </div>

        </div>

        <!-- BOOKING CHART -->
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Seats Booked (Last 7 Days)</h2>
            <canvas id="bookingChart" class="w-full h-64"></canvas>
        </div>

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('bookingChart').getContext('2d');

        const labels = @json($last7Days->pluck('date'));
        const data = @json($last7Days->pluck('seats'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Seats Booked',
                    data: data,
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' seats';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Seats Booked'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>
@endsection