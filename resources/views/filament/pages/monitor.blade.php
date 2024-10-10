<x-filament-panels::page>
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Monitor Data Sensor</h1>

        <!-- Toggle untuk Real-time dan Historis -->
        <div class="filter-options mb-4">
            <label for="data-mode" class="mr-2">Select Mode:</label>
            <select id="data-mode" class="mr-4">
                <option value="realtime" selected>Real-time</option>
                <option value="history">History</option>
            </select>

            <!-- Kontrol untuk pengaturan rentang tanggal dan interval gruping -->
            <div id="date-filters" style="display: none;">
                <label for="start-date" class="mr-2">Start Date:</label>
                <input type="date" id="start-date" value="{{ date('Y-m-d') }}" class="mr-4">

                <label for="end-date" class="mr-2">End Date:</label>
                <input type="date" id="end-date" value="{{ date('Y-m-d') }}" class="mr-4">
            </div>

            <label for="grouping-interval" class="mr-2">Grouping Interval:</label>
            <select id="grouping-interval" class="mr-4">
                <option value="10">10 Seconds</option>
                <option value="30" selected>30 Seconds</option>
                <option value="600">10 Minutes</option>
            </select>

            <button id="update-chart" class="bg-blue-500 text-white px-4 py-2 rounded">Update Chart</button>
            <button id="cancel-chart" class="bg-gray-500 text-white px-4 py-2 rounded" style="display: none;">Cancel</button>
        </div>

        <!-- Canvas untuk grafik individual setiap sensor -->
        <div id="sensor-charts-container">
            @foreach($sensorData as $data)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Sensor: {{ $data['sensor'] }}</h2>
                    <canvas id="sensorChart{{ $loop->index }}" class="w-full h-96"></canvas>
                </div>
            @endforeach
        </div>

        <!-- Canvas untuk grafik yang memuat semua data -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold">Combined Sensor Data</h2>
            <canvas id="combinedSensorChart" class="w-full h-96"></canvas>
        </div>

        <!-- Memuat Chart.js dari CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Membuat chart untuk setiap sensor
                let charts = [];
                @foreach($sensorData as $data)
                    charts.push(createChart("sensorChart{{ $loop->index }}", @json($data['readings']), "{{ $data['sensor'] }}"));
                @endforeach

                // Membuat chart untuk gabungan data semua sensor
                let combinedChart = createCombinedChart(@json($sensorData));

                const dataMode = document.getElementById('data-mode');
                const dateFilters = document.getElementById('date-filters');
                const updateButton = document.getElementById('update-chart');
                const cancelButton = document.getElementById('cancel-chart');

                // Event listener untuk memperbarui visibilitas datepicker dan tombol Cancel berdasarkan pilihan mode
                dataMode.addEventListener('change', function () {
                    if (dataMode.value === 'history') {
                        dateFilters.style.display = 'block';
                        cancelButton.style.display = 'inline-block';
                    } else {
                        dateFilters.style.display = 'none';
                        cancelButton.style.display = 'none';
                    }
                });

                // Event listener untuk tombol Update (mengambil data menggunakan AJAX)
                updateButton.addEventListener('click', function () {
                    const mode = dataMode.value;
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;
                    const interval = document.getElementById('grouping-interval').value;

                    if (mode === 'history') {
                        // Ambil data historis menggunakan AJAX
                        $.ajax({
                            url: '{{ route('filament.monitor.data') }}',
                            type: 'GET',
                            data: {
                                startDate: startDate,
                                endDate: endDate,
                                interval: interval
                            },
                            success: function (data) {
                                updateCharts(data);
                            },
                            error: function () {
                                alert('Failed to fetch historical data. Please try again.');
                            }
                        });
                    }
                });

                // Event listener untuk tombol Cancel
                cancelButton.addEventListener('click', function () {
                    // Reset pilihan mode ke Real-time dan sembunyikan datepickers
                    dataMode.value = 'realtime';
                    dateFilters.style.display = 'none';
                    cancelButton.style.display = 'none';
                    // Bisa tambahkan aksi lain jika ingin kembali ke data real-time
                });

                // Fungsi untuk memperbarui chart dengan data baru
                function updateCharts(sensorData) {
                    // Bersihkan semua grafik sebelumnya
                    charts.forEach(chart => chart.destroy());
                    combinedChart.destroy();

                    const sensorContainer = document.getElementById('sensor-charts-container');
                    sensorContainer.innerHTML = ''; // Bersihkan container grafik sebelumnya

                    charts = []; // Reset charts array

                    // Membuat ulang grafik individual untuk setiap sensor
                    sensorData.forEach((data, index) => {
                        const chartContainer = document.createElement('div');
                        chartContainer.classList.add('mb-6');
                        
                        const title = document.createElement('h2');
                        title.classList.add('text-xl', 'font-semibold');
                        title.innerText = `Sensor: ${data.sensor}`;
                        chartContainer.appendChild(title);

                        const canvas = document.createElement('canvas');
                        canvas.id = `sensorChart${index}`;
                        canvas.classList.add('w-full', 'h-96');
                        chartContainer.appendChild(canvas);
                        
                        sensorContainer.appendChild(chartContainer);

                        const newChart = createChart(canvas.id, data.readings, data.sensor);
                        charts.push(newChart);
                    });

                    // Membuat ulang chart gabungan untuk semua sensor
                    combinedChart = createCombinedChart(sensorData);
                }

                function createChart(chartId, sensorReadings, sensorName) {
                    const ctx = document.getElementById(chartId).getContext('2d');
                    if (sensorReadings.length > 0) {
                        const labels = sensorReadings.map(reading => new Date(reading.time_interval * 1000));
                        const values = sensorReadings.map(reading => reading.avg_value);

                        return new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Data ' + sensorName,
                                    data: values,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    fill: false,
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        type: 'time',
                                        time: {
                                            unit: 'minute',
                                            stepSize: 30, // Interval setiap 30 menit
                                            tooltipFormat: 'yyyy-MM-dd HH:mm:ss',
                                            displayFormats: {
                                                minute: 'HH:mm'  // Format tampilan setiap 30 menit
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Time (30-minute intervals)'
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Value'
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        console.log('Tidak ada data untuk sensor: ' + sensorName);
                    }
                }

                function createCombinedChart(sensorData) {
                    const ctx = document.getElementById('combinedSensorChart').getContext('2d');
                    const datasets = [];

                    sensorData.forEach((data, index) => {
                        const labels = data.readings.map(reading => new Date(reading.time_interval * 1000));
                        const values = data.readings.map(reading => reading.avg_value);

                        datasets.push({
                            label: 'Data ' + data.sensor,
                            data: values,
                            borderColor: `rgba(${54 + index * 30}, ${162 + index * 10}, ${235 - index * 20}, 1)`,
                            fill: false,
                        });
                    });

                    return new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: sensorData.length > 0 ? sensorData[0].readings.map(reading => new Date(reading.time_interval * 1000)) : [],
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'minute',
                                        stepSize: 30, // Interval setiap 30 menit
                                        tooltipFormat: 'yyyy-MM-dd HH:mm:ss',
                                        displayFormats: {
                                            minute: 'HH:mm'  // Format tampilan setiap 30 menit
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Time (30-minute intervals)'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Value'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    </div>
</x-filament-panels::page>
