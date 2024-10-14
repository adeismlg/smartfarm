<x-filament::page>
    <x-filament::card>
        <h1 class="text-2xl font-bold mb-4">Monitor Data Sensor dari Database</h1>

        <!-- Filter periode waktu -->
        <div class="flex items-center space-x-4 mb-4">
            <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" id="start-date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" id="end-date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button id="update-chart" class="bg-blue-500 text-white px-4 py-2 rounded">Update Chart</button>
        </div>

        <!-- Tempat untuk menampilkan chart sensor -->
        <div id="sensor-charts-container">
            @foreach($sensorData as $data)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Sensor: {{ $data['sensor'] }}</h2>
                    <canvas id="sensorChart{{ $loop->index }}" class="w-full h-96"></canvas>
                </div>
            @endforeach
        </div>
    </x-filament::card>

    <!-- Memuat Chart.js, Luxon, dan Chart.js Luxon Adapter -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let charts = [];

            // Membuat chart untuk setiap sensor menggunakan data dari PHP (Blade)
            @foreach($sensorData as $data)
                charts.push(createChart("sensorChart{{ $loop->index }}", @json($data['readings']), "{{ $data['sensor'] }}"));
            @endforeach

            // Event listener untuk tombol Update Chart
            document.getElementById('update-chart').addEventListener('click', function () {
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                // Pastikan tanggal telah dipilih
                if (startDate && endDate) {
                    fetchDataAndUpdateCharts(startDate, endDate);
                } else {
                    alert('Please select a valid date range.');
                }
            });

            // Fungsi untuk mengambil data dari backend dan memperbarui chart
            function fetchDataAndUpdateCharts(startDate, endDate) {
                // Menggunakan AJAX untuk mengambil data baru dari server
                fetch(`/sensor-data?start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        // Bersihkan chart sebelumnya
                        charts.forEach(chart => chart.destroy());
                        charts = [];

                        // Perbarui chart dengan data baru
                        data.forEach((sensorData, index) => {
                            charts.push(createChart(`sensorChart${index}`, sensorData.readings, sensorData.sensor));
                        });
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            // Fungsi untuk membuat chart dengan data yang diambil dari backend
            function createChart(chartId, sensorReadings, sensorName) {
                const ctx = document.getElementById(chartId).getContext('2d');

                if (sensorReadings.length > 0) {
                    // Mengambil timestamp dan nilai sensor dari data
                    const labels = sensorReadings.map(reading => new Date(reading.time_interval * 1000));
                    const values = sensorReadings.map(reading => reading.avg_value);

                    // Membuat grafik dengan Chart.js
                    return new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels, // Waktu sebagai label X
                            datasets: [{
                                label: 'Data ' + sensorName,
                                data: values, // Nilai sensor sebagai data Y
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                fill: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'time', // Menggunakan waktu di sumbu X
                                    time: {
                                        unit: 'minute',
                                        tooltipFormat: 'yyyy-MM-dd HH:mm:ss',
                                        displayFormats: {
                                            minute: 'HH:mm'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Waktu'
                                    }
                                },
                                y: {
                                    beginAtZero: true, // Memulai dari 0 di sumbu Y
                                    title: {
                                        display: true,
                                        text: 'Nilai'
                                    }
                                }
                            }
                        }
                    });
                } else {
                    console.log('Tidak ada data untuk sensor: ' + sensorName);
                }
            }
        });
    </script>
</x-filament::page>
