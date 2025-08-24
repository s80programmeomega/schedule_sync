@extends('layout.base')

@section('title', 'Availability - ScheduleSync')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Availability</h1>
            <p class="text-muted mb-0">Manage your available time slots</p>
        </div>
        <a href="{{ route('availability.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bi bi-plus me-2"></i> Add Availability
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th class="">Timezone</th>
                            <th class="">Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availabilities as $availability)
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse(ucfirst($availability->availability_date))->format('F d, Y') }}</td>
                            <td>
                                {{ $timezones->firstWhere('id', $availability->timezone_id)->display_name ?? '' }}
                            </td>
                            <td class="">{{ \Carbon\Carbon::parse(ucfirst($availability->day_of_week))->format('l') }}</td>
                            <td>{{ $availability->start_time }}</td>
                            <td>{{ $availability->end_time }}</td>
                            <td>
                                <span class="badge {{ $availability->is_available ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $availability->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('availability.show', $availability) }}" class="btn btn-sm btn-light" data-toggle="tooltip" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('availability.edit', $availability) }}"
                                        class="btn btn-sm btn-light" data-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('availability.destroy', $availability) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger"
                                        data-bs-toggle="tooltip" title="Delete"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-calendar-week text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 mb-2">No Availability Set</h5>
                                <p class="text-muted">Add your available time slots</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@if($availabilities->hasPages())
    <div class="mt-4">
        {{ $availabilities->links('pagination.bootstrap') }}
    </div>
@endif
@endsection
