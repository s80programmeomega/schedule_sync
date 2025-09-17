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

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Filters -->
    @include('partials.filters', [
        'filters' => [
            [
                'name' => 'search',
                'type' => 'search',
                'placeholder' => 'Search time slots...',
                'width' => 3,
            ],
            [
                'name' => 'date_from',
                'type' => 'date',
                'placeholder' => 'From Date',
                'width' => 2,
            ],
            [
                'name' => 'date_to',
                'type' => 'date',
                'placeholder' => 'To Date',
                'width' => 2,
            ],
            [
                'name' => 'timezone_id',
                'type' => 'select',
                'placeholder' => 'All Timezones',
                'options' => $timezones->pluck('display_name', 'id')->toArray(),
                'width' => 2,
            ],
            [
                'name' => 'is_available',
                'type' => 'select',
                'placeholder' => 'All Status',
                'options' => ['yes' => 'Available', 'no' => 'Unavailable'],
                'width' => 2,
            ],
            [
                'name' => 'time_range',
                'type' => 'select',
                'placeholder' => 'All Times',
                'options' => [
                    'morning' => 'Morning (Before 12 PM)',
                    'afternoon' => 'Afternoon (12-5 PM)',
                    'evening' => 'Evening (After 5 PM)',
                ],
                'width' => 3,
            ],
        ],
    ])


    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-striped align-middle mb-0">
            <thead>
              <tr>
                <th class="ps-4 py-md-3">Date</th>
                <th class="py-md-3">Timezone</th>
                <th class="py-md-3">Day</th>
                <th class="py-md-3">Start Time</th>
                <th class="py-md-3">End Time</th>
                <th class="py-md-3">Bookings</th>
                <th class="py-md-3">Status</th>
                <th class="pe-4 py-md-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($availabilities as $availability)
                <tr>
                  <td class="ps-4 py-md-3">
                    {{ \Carbon\Carbon::parse(ucfirst($availability->availability_date))->format('F d, Y') }}</td>
                  <td class="py-md-3">
                    {{ $timezones->firstWhere('id', $availability->timezone_id)->display_name ?? '' }}
                  </td>
                  <td class="py-md-3">{{ \Carbon\Carbon::parse(ucfirst($availability->day_of_week))->format('l') }}</td>
                  <td class="py-md-3">{{ $availability->start_time }}</td>
                  <td class="py-md-3">{{ $availability->end_time }}</td>
                  <td class="py-md-3">
                    <span class="badge bg-info">{{ $availability->getBookingCountAttribute() }}</span>
                  </td>
                  <td>
                    <span class="badge {{ $availability->is_available ? 'bg-success' : 'bg-secondary' }}">
                      {{ $availability->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                  </td>
                  <td class="pe-4 py-md-3">
                    <div class="d-flex gap-2">
                      @if ($availability->is_available)
                        <a href="{{ route('availability.slots', $availability) }}" class="btn btn-sm btn-light"
                          data-toggle="tooltip" title="View Slots">
                          <i class="bi bi-clock"></i>
                        </a>
                        <a href="{{ route('availability.show', $availability) }}" class="btn btn-sm btn-light"
                        data-toggle="tooltip" title="View">
                        <i class="bi bi-eye"></i>
                      </a>
                      <a href="{{ route('availability.edit', $availability) }}" class="btn btn-sm btn-light"
                        data-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      @else
                        <span class="btn btn-sm btn-light disabled" data-toggle="tooltip" title="View Slots">
                          <i class="bi bi-clock"></i>
                        </span>
                        <span class="btn btn-sm btn-light disabled" data-toggle="tooltip" title="View Slots">
                            <i class="bi bi-eye"></i>
                        </span>
                        <a href="{{ route('availability.edit', $availability) }}" class="btn btn-sm btn-light"
                        data-toggle="tooltip" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                        </span>
                      @endif

                      <form method="POST" action="{{ route('availability.destroy', $availability) }}"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light text-danger" data-bs-toggle="tooltip"
                          title="Delete" onclick="return confirm('Are you sure?')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <i class="bi bi-calendar-week text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2 py-4">No Availability!</h5>
                    <a href="{{ route('availability.create') }}" class="btn btn-primary">
                      <i class="bi bi-plus me-2"></i> Add Availability
                    </a>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @if ($availabilities->hasPages())
    <div class="mt-4">
      {{ $availabilities->links('pagination.bootstrap') }}
    </div>
  @endif
@endsection
