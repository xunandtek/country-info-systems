@extends('layouts.app')

@section('content')
<div class="card">
    <h2>{{ $country->name_common }}</h2>
    <p><strong>Official:</strong> {{ $country->name_official ?? '—' }}</p>
    <p><strong>Code:</strong> {{ $country->cca2 }} / {{ $country->cca3 }}</p>
    <p><strong>Capital:</strong> {{ $country->capital ?? '—' }}</p>
    <p><strong>Region:</strong> {{ $country->region ?? '—' }}</p>
    <p><strong>Subregion:</strong> {{ $country->subregion ?? '—' }}</p>
    <p><strong>Population:</strong> {{ number_format($country->population ?? 0) }}</p>

    @if($country->flag_png)
        <p><img src="{{ $country->flag_png }}" alt="Flag" style="max-width:160px;"></p>
    @endif

    <h3>Borders</h3>
    @if($country->borders->count() === 0)
        <p>None</p>
    @else
        <ul>
            @foreach($country->borders as $b)
                <li>
                    <a href="{{ route('countries.show', $b->cca3) }}">{{ $b->name_common }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
