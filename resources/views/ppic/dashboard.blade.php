@extends('layouts.ppic')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard PPIC</h1>

    <form method="GET" action="{{ route('ppic.dashboard') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="tahun">Tahun</label>
                <select name="tahun" class="form-control">
    @foreach ($availableTahun as $th)
        <option value="{{ $th }}" {{ $th == $selectedTahun ? 'selected' : '' }}>{{ $th }}</option>
    @endforeach
</select>
            </div>
            <div class="col-md-4">
                <label for="kategori">Kategori</label>
                <select name="kategori" class="form-control">
                    <option value="produksi" {{ $kategori === 'produksi' ? 'selected' : '' }}>Produksi</option>
                    <option value="bahanbaku" {{ $kategori === 'bahanbaku' ? 'selected' : '' }}>Bahan Baku</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </form>

    @php $chartWithId = []; @endphp
    @forelse ($dataChart as $label => $chart)
        @php
            $canvasId = 'chart_' . \Illuminate\Support\Str::slug($label, '_');
            $chartWithId[$canvasId] = $chart;
        @endphp
        <div class="card mb-4">
            <div class="card-header">
                Grafik Perbandingan: {{ $label }}
            </div>
            <div class="card-body">
                <canvas id="{{ $canvasId }}" width="400" height="200"></canvas>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">Tidak ada data grafik yang tersedia.</div>
    @endforelse
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dataChart = @json($chartWithId);

    Object.entries(dataChart).forEach(([canvasId, data]) => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) {
            console.warn("Canvas not found for:", canvasId);
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.bulan,
                datasets: [
                    {
                        label: 'Aktual',
                        data: data.aktual,
                        borderColor: 'blue',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Hasil',
                        data: data.hasil,
                        borderColor: 'red',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: canvasId.replace('chart_', '').replace(/_/g, ' ').toUpperCase()
                    }
                }
            }
        });
    });
</script>
@endsection
