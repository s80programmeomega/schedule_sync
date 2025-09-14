<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3" id="filterForm">
            @foreach($filters as $filter)
                <div class="col-md-{{ $filter['width'] ?? 3 }}">
                    @if($filter['type'] === 'select')
                        <select name="{{ $filter['name'] }}" class="form-select">
                            <option value="">{{ $filter['placeholder'] }}</option>
                            @foreach($filter['options'] as $value => $label)
                                <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filter['type'] === 'search')
                        <input type="text" name="{{ $filter['name'] }}" class="form-control"
                               placeholder="{{ $filter['placeholder'] }}"
                               value="{{ request($filter['name']) }}">
                    @elseif($filter['type'] === 'date')
                        <div>
                            <input type="date" name="{{ $filter['name'] }}" class="form-control"
                                   value="{{ request($filter['name']) }}">
                            <label class="form-text small text-muted">{{ $filter['placeholder'] }}</label>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            @if(request()->hasAny(collect($filters)->pluck('name')->toArray()))
                <div class="col-md-2">
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            @endif
        </form>
    </div>
</div>
