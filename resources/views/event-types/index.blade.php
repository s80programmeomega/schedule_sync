@extends('layout.base')

@section('title', 'Event Types - ScheduleSync')

@section('content')
    <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold">Event Types</h1>
                <p class="text-muted mb-0">Manage your available meeting types</p>
            </div>
            <a href="{{ route('event-types.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus me-2"></i> Create Event Type
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            @forelse($eventTypes as $eventType)
                <div class="col-md-4 mb-4">

                    <div class="event-type-card h-100 bg-white d-flex flex-column">
                        <div class="p-4">
                            <a href="{{ route('event-types.show', $eventType) }}" class="text-decoration-none flex-grow-1">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="event-type-icon me-3"
                                        style="background-color: {{ $eventType->color ?? '#6366f1' }}20; color: {{ $eventType->color ?? '#6366f1' }}">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <h3 class="h5 mb-0 fw-semibold">{{ $eventType->name }}</h3>
                                </div>
                                <p class="text-muted mb-3">{{ $eventType->description ?: 'No description provided' }}</p>
                                <div class="d-flex align-items-center text-muted small mb-2">
                                    <i class="bi bi-clock me-2"></i>
                                    <span>{{ $eventType->duration }} min</span>
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <span>{{ $eventType->bookings_count }} bookings</span>
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <span
                                        class="text-truncate">{{ ucfirst(str_replace('_', ' ', $eventType->location_type)) }}</span>
                                </div>
                            </a>
                            </div>
                        <div class="card-footer bg-light border-top py-3 px-4 mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="eventActive{{ $eventType->id }}"
                                        {{ $eventType->is_active ? 'checked' : '' }}
                                        onchange="toggleEventType({{ $eventType->id }})">
                                    <label class="form-check-label" for="eventActive{{ $eventType->id }}">Active</label>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('event-types.edit', $eventType) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item" href="#"
                                                onclick="copyLink('{{ $eventType->id }}')">
                                                <i class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('event-types.destroy', $eventType) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                    onclick="return confirm('Are you sure you want to delete this event type?')">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 mb-2">No Event Types Yet</h4>
                        <p class="text-muted mb-4">Create your first event type to start accepting bookings</p>
                        <a href="{{ route('event-types.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus me-2"></i> Create Event Type
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    @if($eventTypes->hasPages())
        <div class="mt-4">
            {{ $eventTypes->links('pagination.bootstrap') }}
        </div>
    @endif

    <script>
        function toggleEventType(id) {
            fetch(`/event-types/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(() => location.reload());
        }

        function deleteEventType(id) {
            if (confirm('Are you sure you want to delete this event type?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/event-types/${id}`;
                form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
