@extends('layout.base') @section('content')
  <!-- Main Content -->
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    @if (!auth()->user()->hasVerifiedEmail())
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Email Verification Required:</strong>
        Please verify your email address to access all features.
        <a href="{{ route('verification.notice') }}" class="alert-link">Verify now</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Show success message when email is verified --}}
    @if (request('verified') == 1)
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Email Verified!</strong> Your email address has been successfully verified.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
        <p class="text-muted mb-0">Manage your availability and bookings</p>
      </div>
      <a href="{{ route('event-types.create') }}" class="btn btn-primary d-flex align-items-center" id="createEventBtn">
        <i class="bi bi-plus me-2"></i> Create Event Type
      </a>
      {{--
            <button class="btn btn-primary d-flex align-items-center" id="createEventBtn">
                <i class="bi bi-plus me-2"></i> Create Event Type
            </button>
            --}}
    </div>


    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3 mb-md-0">
        <a href="{{ route('bookings.index', ['status' => 'scheduled']) }}" class="text-decoration-none">
          <div class="card border-0 shadow-sm animate-fade-in">
            <div class="card-body p-4">
              <div class="d-flex align-items-center">
                <div class="me-3 p-3 rounded-circle bg-primary bg-opacity-10">
                  <i class="bi bi-calendar-check text-primary fs-4"></i>
                </div>
                <div>
                  <h5 class="card-title h2 mb-0 fw-bold">
                    {{ $upcomingMeetings->count() }}
                  </h5>
                  <p class="card-text text-muted mb-0">
                    Upcoming Meetings
                  </p>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm animate-fade-in animate-delay-1">
            <div class="card-body p-4">
              <div class="d-flex align-items-center">
                <div class="me-3 p-3 rounded-circle bg-success bg-opacity-10">
                  <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <div>
                  <h5 class="card-title h2 mb-0 fw-bold">
                    {{ $completedMeetings }}
                  </h5>
                  <p class="card-text text-muted mb-0">
                    Completed Meetings
                  </p>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm animate-fade-in animate-delay-2">
            <div class="card-body p-4">
              <div class="d-flex align-items-center">
                <div class="me-3 p-3 rounded-circle bg-danger bg-opacity-10">
                  <i class="bi bi-x-circle text-danger fs-4"></i>
                </div>
                <div>
                  <h5 class="card-title h2 mb-0 fw-bold">
                    {{ $cancelledMeetings }}
                  </h5>
                  <p class="card-text text-muted mb-0">
                    Cancelled Meetings
                  </p>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    @if (auth()->user()->teams->isNotEmpty())
      <!-- Team Overview -->
      <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="h4 fw-bold mb-0">Team Overview</h2>
          <a href="{{ route('teams.index') }}" class="text-decoration-none">View All Teams</a>
        </div>

        <div class="row">
          @foreach (auth()->user()->teams->take(2) as $team)
            <div class="col-md-6 mb-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <a href="{{ route('teams.show', $team) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center mb-3">
                          <div class="team-avatar me-3">
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                              style="width: 40px; height: 40px;">
                              {{ substr($team->name, 0, 2) }}
                            </div>
                          </div>
                          <div>
                            <h6 class="mb-0 text-dark">{{ $team->name }}</h6>
                            <small class="text-muted">{{ auth()->user()->getRoleInTeam($team) }}</small>
                          </div>
                        </div>
                      </a>
                  <div class="row text-center">
                    <div class="col-4">
                      <a href="{{ route('teams.members.index', $team) }}" class="text-decoration-none">
                        <div class="d-flex flex-column align-items-center">
                          <i class="bi bi-people text-primary mb-1"></i>
                          <div class="fw-bold">{{ $team->activeMembers->count() }}</div>
                          <small class="text-muted">Members</small>
                        </div>
                      </a>
                    </div>
                    <div class="col-4">
                      <a href="{{ route('event-types.index', ['team' => $team->id]) }}" class="text-decoration-none">
                        <div class="d-flex flex-column align-items-center">
                          <i class="bi bi-calendar-event text-success mb-1"></i>
                          <div class="fw-bold">{{ $team->eventTypes->count() }}</div>
                          <small class="text-muted">Events</small>
                        </div>
                      </a>
                    </div>
                    <div class="col-4">
                      <a href="{{ route('contacts.index', ['team' => $team->id]) }}" class="text-decoration-none">
                        <div class="d-flex flex-column align-items-center">
                          <i class="bi bi-person-lines-fill text-info mb-1"></i>
                          <div class="fw-bold">{{ $team->contacts->count() }}</div>
                          <small class="text-muted">Contacts</small>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif


    <!-- Event Types -->
    <div class="mb-5 animate-fade-in animate-delay-3">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0">Your Event Types</h2>
        <a href="{{ route('event-types.index') }}" class="text-decoration-none">View All</a>
      </div>

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
                    <span class="text-truncate">{{ ucfirst(str_replace('_', ' ', $eventType->location_type)) }}</span>
                  </div>
                </a>
              </div>
              <div class="card-footer bg-light border-top py-3 px-4 mt-auto">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="form-check form-switch">
                    <form method="POST" action="{{ route('event-types.toggle', $eventType) }}"
                      style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="eventActive{{ $eventType->id }}"
                          {{ $eventType->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                        <input type="hidden" name="is_active" value="0">
                        <input type="hidden" name="is_active" value="{{ $eventType->is_active ? '0' : '1' }}">
                        <label class="form-check-label" for="eventActive{{ $eventType->id }}">Active</label>
                      </div>
                    </form>

                  </div>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li><a class="dropdown-item" href="{{ route('event-types.edit', $eventType) }}">
                          <i class="bi bi-pencil me-2"></i>Edit</a></li>
                      <li><a class="dropdown-item" href="#" onclick="copyLink('{{ $eventType->id }}')">
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

    <!-- Upcoming Meetings -->
    <div class="mb-5">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0">Upcoming Meetings</h2>
        <div>
          <div class="btn-group" role="group">
            <button type="button"
              class="btn btn-sm btn-outline-primary {{ request('filter', 'day') == 'day' ? 'filter-active' : '' }}"
              data-filter="day">
              Today
            </button>
            <button type="button"
              class="btn btn-sm btn-outline-primary {{ request('filter') == 'week' ? 'filter-active' : '' }}"
              data-filter="week">
              This Week
            </button>
            <button type="button"
              class="btn btn-sm btn-outline-primary {{ request('filter') == 'month' ? 'filter-active' : '' }}"
              data-filter="month">
              This Month
            </button>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-striped  table-responsive align-middle  mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="ps-4">Attendees</th>
                  <th>Event Type</th>
                  <th>Date & Time</th>
                  <th>Duration</th>
                  <th class="pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($upcomingMeetings as $upcomingMeeting)
                  <tr>
                    <td class="ps-4">
                      <div class="d-flex align-items-center">
                        {{-- <img
                                                    src="https://ui-avatars.com/api/?name={{ $upcomingMeeting->attendee_name }}&background=4f46e5&color=fff"
                                                    class="rounded-circle me-3" width="42" height="42"
                                                    alt="{{ $upcomingMeeting->attendee_name }}" /> --}}
                        <div class="px-4">
                          <h6 class="badge fw-bold bg-secondary">
                            {{ $upcomingMeeting->attendees->count() }}
                          </h6>
                          {{-- <span class="text-muted small">{{ $upcomingMeeting->attendee_email }}</span> --}}
                        </div>
                      </div>
                    </td>
                    <td>
                      {{ $upcomingMeeting->eventType?->name ?? 'N/A' }}
                    </td>

                    <td>
                      <div>
                        {{ \Carbon\Carbon::parse($upcomingMeeting->getFormattedDateTimeAttribute())->format('M j, Y g:i A') }}
                      </div>
                      <span class="badge bg-warning text-dark">
                        {{ \Carbon\Carbon::parse($upcomingMeeting->booking_date)->setTimeFromTimeString($upcomingMeeting->start_time)->diffForHumans() }}

                      </span>
                    </td>
                    <td>
                      {{ $upcomingMeeting->eventType?->duration ?? 'N/A' }}
                      min
                    </td>

                    <td class="pe-4">
                      <div class="d-flex">
                        <button class="btn btn-sm btn-light me-2" title="Reschedule">
                          <i class="bi bi-calendar"></i>
                        </button>
                        <button onclick="cancelBooking({{ $upcomingMeeting->id }})" class="btn btn-sm btn-light me-2"
                          title="Cancel">
                          <i class="bi bi-x-lg"></i>
                        </button>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <a class="dropdown-item" href="#"><i class="bi bi-clipboard me-2"></i>Copy Invite
                                Link</a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i>Email
                                Attendee</a>
                            </li>
                            <li>
                              <hr class="dropdown-divider" />
                            </li>
                            <li>
                              <a class="dropdown-item text-danger" href="#"><i
                                  class="bi bi-trash me-2"></i>Cancel Meeting</a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">
                      No upcoming meetings
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar Preview -->
    <div class="row">
      <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm calendar-grid">
          <div class="calendar-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-semibold">{{ $calendarData['current_month']->format('F Y') }}</h5>
              <div>
                <a href="?month={{ $calendarData['prev_month']->format('Y-m') }}"
                  class="btn btn-sm btn-primary bg-opacity-75 border-0 me-2">
                  <i class="bi bi-chevron-left"></i>
                </a>
                <a href="?month={{ $calendarData['next_month']->format('Y-m') }}"
                  class="btn btn-sm btn-primary bg-opacity-75 border-0">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </div>
            </div>
            <div class="row text-center">
              <div class="col">Mon</div>
              <div class="col">Tue</div>
              <div class="col">Wed</div>
              <div class="col">Thu</div>
              <div class="col">Fri</div>
              <div class="col">Sat</div>
              <div class="col">Sun</div>
            </div>
          </div>
          <div class="card-body p-0">
            @foreach (array_chunk($calendarData['calendar_days'], 7) as $week)
              <div class="row g-0">
                @foreach ($week as $day)
                  @php
                    // Check if this day has availability and get its ID
                    $availabilityId = null;
                    if ($day['has_availability']) {
                        $availability = \App\Models\Availability::where('user_id', auth()->id())
                            ->whereDate('availability_date', $day['date'])
                            ->first();
                        $availabilityId = $availability?->id;
                    }
                  @endphp

                  <div
                    class="col calendar-day {{ !$day['is_current_month'] ? 'disabled' : '' }} {{ $day['is_today'] ? 'active' : '' }}"
                    @if ($availabilityId) onclick="window.location.href='{{ route('availability.show', $availabilityId) }}'"
                                        style="cursor: pointer;" @endif>
                    <div class="p-2">
                      <div class="day-number mb-2">{{ $day['day'] }}</div>

                      @if ($day['is_today'])
                        <div class="small py-1 px-2 bg-warning text-dark rounded mb-1 text-truncate">
                          Today
                        </div>
                      @endif

                      @if ($day['bookings_count'] > 0)
                        <a href="{{ route('bookings.index', [
                            'date_from' => $day['date']->format('Y-m-d'),
                            'date_to' => $day['date']->format('Y-m-d'),
                        ]) }}"
                          class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate text-decoration-none d-block"
                          onclick="event.stopPropagation();">
                          {{ $day['bookings_count'] }}
                          {{ $day['bookings_count'] == 1 ? 'booking' : 'bookings' }}
                        </a>
                      @endif

                      @if ($day['has_availability'] && $day['bookings_count'] == 0)
                        <div class="small py-1 px-2 bg-success text-white rounded mb-1 text-truncate">
                          Available
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach

              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="card-header border-bottom bg-white py-3 mb-4">
          <h5 class="card-title mb-0 fw-semibold text-center">Today's Schedule</h5>
        </div>
        <div class="card-body">
          @forelse($todaysBookings as $booking)
            <div class="d-flex align-items-center mb-3">
              <div class="bg-primary text-white rounded-circle p-2 me-3">
                <i class="bi bi-calendar-check"></i>
              </div>
              <div>
                <h6 class="mb-0 fw-semibold">
                  {{ $booking->attendee_name }}
                </h6>
                <p class="text-muted mb-0 small">
                  {{ $booking->eventType?->name ?? 'Meeting' }} •
                  {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} -
                  {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                </p>
              </div>
            </div>
          @empty
            <div class="text-center py-3">
              <i class="bi bi-calendar-x text-muted" style="font-size: 2rem"></i>
              <p class="text-muted mb-0 mt-2">
                No meetings scheduled for today
              </p>
            </div>
          @endforelse
          <div class="mb-3">
            <div class="border-top my-4 pt-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-0">Available Time Slots</h6>
                <form method="GET" class="d-flex align-items-center gap-2">
                  <input type="hidden" name="filter" value="{{ request('filter', 'day') }}">
                  <select name="duration" class="form-select form-select-sm" style="width: auto;"
                    onchange="this.form.submit()">
                    <option value="15" {{ $duration == 15 ? 'selected' : '' }}>15min</option>
                    <option value="30" {{ $duration == 30 ? 'selected' : '' }}>30min</option>
                    <option value="45" {{ $duration == 45 ? 'selected' : '' }}>45min</option>
                    <option value="60" {{ $duration == 60 ? 'selected' : '' }}>1hr</option>
                    <option value="90" {{ $duration == 90 ? 'selected' : '' }}>1hr 30min</option>
                    <option value="120" {{ $duration == 120 ? 'selected' : '' }}>2hr</option>
                  </select>
                </form>
              </div>
            </div>
            @forelse($availableSlots as $slot)
              @php
                $now = now();
                // dd($now);
                $slotStart = \Carbon\Carbon::parse(today()->toDateString() . ' ' . $slot['start_time']);
                $slotEnd = \Carbon\Carbon::parse(today()->toDateString() . ' ' . $slot['end_time']);

                // Skip past slots
                if ($slotEnd->lt($now)) {
                    continue;
                }

                $cssClass = 'time-slot text-decoration-none';
                if ($slot['is_occupied']) {
                    if ($now->between($slotStart, $slotEnd)) {
                        $cssClass .= ' active';
                    } else {
                        $cssClass .= ' upcoming';
                    }
                }
              @endphp

              <a href="#" class="{{ $cssClass }}">{{ $slot['formatted_time'] }}</a>
            @empty
              <p class="text-muted small">No available slots for today</p>
            @endforelse


          </div>

          <div class="text-center mt-3">
            <a href="{{ route('bookings.create') }}" class="btn btn-primary w-100">
              <i class="bi bi-plus-circle me-2"></i> Book An Event
            </a>
          </div>
        </div>
      </div>

      <!-- <div class="col-lg-4 mb-4">
                                      <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header border-bottom bg-white py-3">
                                          <h5 class="card-title mb-0 fw-semibold">Today's Schedule</h5>
                                        </div>
                                        <div class="card-body">
                                          <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3">
                                              <i class="bi bi-calendar-check"></i>
                                            </div>
                                            <div>
                                              <h6 class="mb-0 fw-semibold">Sarah Johnson</h6>
                                              <p class="text-muted mb-0 small">30 Minute Consultation • 2:30 PM</p>
                                            </div>
                                          </div>

                                          <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3">
                                              <i class="bi bi-calendar-check"></i>
                                            </div>
                                            <div>
                                              <h6 class="mb-0 fw-semibold">Michael Chen</h6>
                                              <p class="text-muted mb-0 small">15 Minute Meeting • 4:00 PM</p>
                                            </div>
                                          </div>

                                          <div class="border-top my-4 pt-4">
                                            <h6 class="fw-semibold mb-3">Available Time Slots</h6>

                                            <div class="mb-3">
                                              <a href="#" class="time-slot">10:00 AM - 10:30 AM</a>
                                              <a href="#" class="time-slot">11:30 AM - 12:00 PM</a>
                                              <a href="#" class="time-slot">1:00 PM - 1:30 PM</a>
                                              <a href="#" class="time-slot selected">2:30 PM - 3:00 PM</a>
                                              <a href="#" class="time-slot">4:30 PM - 5:00 PM</a>
                                            </div>
                                          </div>

                                          <div class="text-center mt-3">
                                            <button class="btn btn-primary w-100">
                                              <i class="bi bi-plus-circle me-2"></i> Add Event
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div> -->
    </div>
  </div>

  <script>
    // This script applies the filter parameter to the request
    document.querySelectorAll("[data-filter]").forEach((button) => {
      button.addEventListener("click", function() {
        const filter = this.dataset.filter;
        const url = new URL(window.location);
        url.searchParams.set("filter", filter);
        window.location.href = url.toString();
      });
    });

    function cancelBooking(id) {
      const reason = prompt('Cancellation reason (optional):');
      if (reason !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bookings/${id}/cancel`;
        form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="cancellation_reason" value="${reason}">
                    `;
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
@endsection
