@extends('layouts.manajer')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="bahanbakuTabs" role="tablist">
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

    <div class="tab-content" id="bahanbakuTabsContent">
        {{-- Tambang --}}
        <div class="tab-pane fade show active" id="tambang" role="tabpanel">
            @include('manajer.partials.bahanbaku-table', ['data' => $tambang, 'kategori' => 'Tambang'])
        </div>

        {{-- Jaring --}}
        <div class="tab-pane fade" id="jaring" role="tabpanel">
            @include('manajer.partials.bahanbaku-table', ['data' => $jaring, 'kategori' => 'Jaring'])
        </div>

        {{-- Benang --}}
        <div class="tab-pane fade" id="benang" role="tabpanel">
            @include('manajer.partials.bahanbaku-table', ['data' => $benang, 'kategori' => 'Benang'])
        </div>
    </div>
</div>
@endsection
