<x-filament-panels::page>
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Monitor Data Sensor</h1>

        <!-- Memuat Chart.js dari CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Memuat Luxon dan Adapter untuk Chart.js dari CDN -->
        <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>

        <!-- Looping untuk setiap sensor dan membuat grafik terpisah -->
        @foreach ($this->getSensorsWithReadings() as $sensor)
            <div class="mb-6">
                <h2 class="text-xl font-semibold">Sensor: {{ $sensor->name }}</h2>

                <!-- Canvas untuk setiap grafik sensor -->
                <canvas id="sensorChart{{ $sensor->id }}"></canvas>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var ctx = document.getElementById('sensorChart{{ $sensor->id }}').getContext('2d');

                    // Ambil data pembacaan (readings) sensor dalam format JSON
                    var sensorReadings = @json($sensor->readings);

                    // Periksa apakah ada pembacaan untuk sensor ini
                    if (sensorReadings.length > 0) {
                        // Ambil label (timestamps) dan value dari readings
                        var labels = sensorReadings.map(reading => new Date(reading.created_at));
                        var values = sensorReadings.map(reading => reading.value);

                        // Hitung rentang waktu antara pembacaan pertama dan terakhir
                        var firstReading = labels[0];
                        var lastReading = labels[labels.length - 1];
                        var timeDiff = (lastReading - firstReading) / 1000; // Waktu dalam detik

                        // Menentukan unit waktu secara dinamis berdasarkan rentang waktu
                        let timeUnit = 'minute'; // Default unit
                        if (timeDiff > 60 * 60 * 24) {
                            timeUnit = 'day';  // Jika rentang lebih dari satu hari
                        } else if (timeDiff > 60 * 60) {
                            timeUnit = 'hour'; // Jika rentang lebih dari satu jam
                        } else if (timeDiff > 60) {
                            timeUnit = 'minute'; // Jika rentang lebih dari satu menit
                        }

                        // Debugging untuk melihat data di Console browser (Opsional)
                        console.log('Sensor ID: {{ $sensor->id }}', sensorReadings, 'Time Unit:', timeUnit);

                        // Buat chart untuk setiap sensor
                        var chart = new Chart(ctx, {
                            type: 'line',  // Menggunakan Bar Chart
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Pembacaan {{ $sensor->name }}',
                                    data: values,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',  // Warna batang
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1,
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    },
                                    x: {
                                        type: 'time',  // Menggunakan skala waktu di sumbu X
                                        time: {
                                            tooltipFormat: 'yyyy-MM-dd HH:mm',
                                            unit: timeUnit  // Tampilkan interval waktu yang dinamis
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.log('Tidak ada data pembacaan untuk sensor: {{ $sensor->id }}');
                    }
                });
            </script>
        @endforeach
    </div>
</x-filament-panels::page>
