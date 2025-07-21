@extends('layouts.ppic')

@section('title', 'Dashboard PPIC')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard PPIC</h1>

    {{-- Grafik Peramalan --}}
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Aktual vs Hasil Peramalan</h6>
                </div>
                <div class="card-body">
                    {{-- Dropdown Filter --}}
                    <select id="filterGrafik" class="form-control mb-3">
                        <option value="">-- Pilih Grafik --</option>
                        @foreach(array_keys($dataChart) as $label)
                            <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Canvas Grafik --}}
                    <canvas id="chartCanvas" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allData = @json($dataChart);
    const ctx = document.getElementById('chartCanvas').getContext('2d');
    let chart;

    function renderChart(label) {
        const chartData = allData[label];

        if (chart) chart.destroy(); // Hapus grafik sebelumnya

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.tahun,
                datasets: [
                    {
                        label: 'Aktual',
                        data: chartData.aktual,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Hasil Peramalan',
                        data: chartData.hasil,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Grafik Peramalan ' + label
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    document.getElementById('filterGrafik').addEventListener('change', function () {
        const selected = this.value;
        if (selected) {
            renderChart(selected);
        } else {
            if (chart) chart.destroy();
        }
    });
});
</script>
@endpush
