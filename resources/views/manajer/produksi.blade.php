@extends('layouts.manajer')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="produksiTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tambang-tab" data-toggle="tab" href="#tambang" role="tab">Tambang</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="jaring-tab" data-toggle="tab" href="#jaring" role="tab">Jaring</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="benang-tab" data-toggle="tab" href="#benang" role="tab">Benang</a>
        </li>
    </ul>

    <div class="tab-content" id="produksiTabsContent">
        {{-- Tambang --}}
        <div class="tab-pane fade show active" id="tambang" role="tabpanel">
            @include('manajer.partials.produksi-table', ['data' => $tambang])
        </div>

        {{-- Jaring --}}
        <div class="tab-pane fade" id="jaring" role="tabpanel">
            @include('manajer.partials.produksi-table', ['data' => $jaring])
        </div>

        {{-- Benang --}}
        <div class="tab-pane fade" id="benang" role="tabpanel">
            @include('manajer.partials.produksi-table', ['data' => $benang])
        </div>
    </div>
</div>
@endsection
