@extends('layouts.app')

@section('content')
<div class="row">
    <div style="flex: 2;">
        <div class="card">
            <form method="GET" action="{{ route('countries.index') }}">
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search name or capital..." />
                    <select name="region">
                        <option value="">All regions</option>
                        @foreach($regions as $r)
                            <option value="{{ $r }}" @selected($region === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Search</button>
                    <a href="{{ route('countries.index') }}">Reset</a>
                </div>
            </form>
        </div>

        <div style="margin-top:16px;">
            <div class="card">
                <h3>Countries</h3>
                <p>Showing {{ $countries->total() }} results</p>

                <ul>
                    @foreach($countries as $c)
                        <li>
                            <a href="{{ route('countries.show', $c->cca3) }}">
                                {{ $c->name_common }}
                            </a>
                            @if($c->capital) — <small>{{ $c->capital }}</small> @endif
                        </li>
                    @endforeach
                </ul>

                {{ $countries->links() }}
            </div>
        </div>
    </div>

    <div style="flex: 1;">
        <div class="card">
            <h3>Trending (24h)</h3>
            @if($trending->count() === 0)
                <p><small>No trending data yet. Run: <code>php artisan trending:recalculate</code></small></p>
            @else
                <ul>
                    @foreach($trending as $t)
                        <li>
                            <a href="{{ route('countries.show', $t->cca3) }}">
                                {{ $t->name_common }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
