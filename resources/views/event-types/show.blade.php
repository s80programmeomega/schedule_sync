@extends('layout.base')

@section('title')
    {{ $eventType->name . ' - Event Type' }}
@endsection


@section('content')
    <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $eventType->name }}</h5>
                <span class="badge {{ $eventType->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $eventType->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $eventType->description }}</p>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Duration:</strong> {{ $eventType->duration }} minutes
                    </div>
                    <div class="col-md-6">
                        <strong>Location:</strong> {{ ucfirst(str_replace('_', ' ', $eventType->location_type)) }}
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('event-types.edit', $eventType) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('event-types.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
