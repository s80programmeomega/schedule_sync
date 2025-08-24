@extends('layout.base')

@section('content')
  <!-- Main Content -->
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5" data-aos="zoom-out" data-aos-duration="1000">
	<div class="d-flex justify-content-between align-items-center mb-4">
	  <div>
		<h1 class="h3 mb-0 fw-bold">Dashboard</h1>
		<p class="text-muted mb-0">Manage your availability and bookings</p>
	  </div>
	  <a href="{{ route('event-types.create') }}" class="btn btn-primary d-flex align-items-center" id="createEventBtn">
		<i class="bi bi-plus me-2"></i> Create Event Type
	  </a>
	  {{-- <button class="btn btn-primary d-flex align-items-center" id="createEventBtn">
		<i class="bi bi-plus me-2"></i> Create Event Type
	  </button> --}}
	</div>

	<!-- Statistics Cards -->
	<div class="row mb-4">
	  <div class="col-md-4 mb-3 mb-md-0">
		<div class="card border-0 shadow-sm animate-fade-in">
		  <div class="card-body p-4">
			<div class="d-flex align-items-center">
			  <div class="me-3 p-3 rounded-circle bg-primary bg-opacity-10">
				<i class="bi bi-calendar-check text-primary fs-4"></i>
			  </div>
			  <div>
				<h5 class="card-title h2 mb-0 fw-bold">24</h5>
				<p class="card-text text-muted mb-0">Upcoming Meetings</p>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="col-md-4 mb-3 mb-md-0">
		<div class="card border-0 shadow-sm animate-fade-in animate-delay-1">
		  <div class="card-body p-4">
			<div class="d-flex align-items-center">
			  <div class="me-3 p-3 rounded-circle bg-success bg-opacity-10">
				<i class="bi bi-check-circle text-success fs-4"></i>
			  </div>
			  <div>
				<h5 class="card-title h2 mb-0 fw-bold">18</h5>
				<p class="card-text text-muted mb-0">Completed Meetings</p>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="col-md-4 mb-3 mb-md-0">
		<div class="card border-0 shadow-sm animate-fade-in animate-delay-2">
		  <div class="card-body p-4">
			<div class="d-flex align-items-center">
			  <div class="me-3 p-3 rounded-circle bg-danger bg-opacity-10">
				<i class="bi bi-x-circle text-danger fs-4"></i>
			  </div>
			  <div>
				<h5 class="card-title h2 mb-0 fw-bold">3</h5>
				<p class="card-text text-muted mb-0">Cancelled Meetings</p>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Event Types -->
	<div class="mb-5 animate-fade-in animate-delay-3">
	  <div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="h4 fw-bold mb-0">Your Event Types</h2>
		<a href="#" class="text-decoration-none">View All</a>
	  </div>

	  <div class="row">
		<div class="col-md-4 mb-4">
		  <div class="event-type-card h-100 bg-white">
			<div class="p-4">
			  <div class="d-flex align-items-center mb-3">
				<div class="event-type-icon me-3">
				  <i class="bi bi-chat-square-text"></i>
				</div>
				<h3 class="h5 mb-0 fw-semibold">15 Minute Meeting</h3>
			  </div>
			  <p class="text-muted mb-3">Quick sync to discuss important matters</p>
			  <div class="d-flex align-items-center text-muted small">
				<i class="bi bi-clock me-2"></i>
				<span>15 min</span>
				<span class="mx-2">•</span>
				<i class="bi bi-link-45deg me-2"></i>
				<span class="text-truncate">scheduleSync.com/john/quick-chat</span>
			  </div>
			</div>
			<div class="card-footer bg-light border-top py-3 px-4">
			  <div class="d-flex justify-content-between align-items-center">
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" id="eventActive1" checked>
				  <label class="form-check-label" for="eventActive1">Active</label>
				</div>
				<div class="dropdown">
				  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
					<i class="bi bi-three-dots"></i>
				  </button>
				  <ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-clipboard me-2"></i>Copy
						Link</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
				  </ul>
				</div>
			  </div>
			</div>
		  </div>
		</div>

		<div class="col-md-4 mb-4">
		  <div class="event-type-card h-100 bg-white">
			<div class="p-4">
			  <div class="d-flex align-items-center mb-3">
				<div class="event-type-icon me-3">
				  <i class="bi bi-people"></i>
				</div>
				<h3 class="h5 mb-0 fw-semibold">30 Minute Consultation</h3>
			  </div>
			  <p class="text-muted mb-3">In-depth discussion about projects and goals</p>
			  <div class="d-flex align-items-center text-muted small">
				<i class="bi bi-clock me-2"></i>
				<span>30 min</span>
				<span class="mx-2">•</span>
				<i class="bi bi-link-45deg me-2"></i>
				<span class="text-truncate">scheduleSync.com/john/consultation</span>
			  </div>
			</div>
			<div class="card-footer bg-light border-top py-3 px-4">
			  <div class="d-flex justify-content-between align-items-center">
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" id="eventActive2" checked>
				  <label class="form-check-label" for="eventActive2">Active</label>
				</div>
				<div class="dropdown">
				  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
					<i class="bi bi-three-dots"></i>
				  </button>
				  <ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-clipboard me-2"></i>Copy Link</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
				  </ul>
				</div>
			  </div>
			</div>
		  </div>
		</div>

		<div class="col-md-4 mb-4">
		  <div class="event-type-card h-100 bg-white">
			<div class="p-4">
			  <div class="d-flex align-items-center mb-3">
				<div class="event-type-icon me-3">
				  <i class="bi bi-laptop"></i>
				</div>
				<h3 class="h5 mb-0 fw-semibold">60 Minute Workshop</h3>
			  </div>
			  <p class="text-muted mb-3">A collaborative session to work through challenges</p>
			  <div class="d-flex align-items-center text-muted small">
				<i class="bi bi-clock me-2"></i>
				<span>60 min</span>
				<span class="mx-2">•</span>
				<i class="bi bi-link-45deg me-2"></i>
				<span class="text-truncate">scheduleSync.com/john/workshop</span>
			  </div>
			</div>
			<div class="card-footer bg-light border-top py-3 px-4">
			  <div class="d-flex justify-content-between align-items-center">
				<div class="form-check form-switch">
				  <input class="form-check-input" type="checkbox" id="eventActive3" checked>
				  <label class="form-check-label" for="eventActive3">Active</label>
				</div>
				<div class="dropdown">
				  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
					<i class="bi bi-three-dots"></i>
				  </button>
				  <ul class="dropdown-menu dropdown-menu-end">
					<li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-clipboard me-2"></i>Copy Link</a></li>
					<li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
				  </ul>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Upcoming Meetings -->
	<div class="mb-5">
	  <div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="h4 fw-bold mb-0">Upcoming Meetings</h2>
		<div>
		  <div class="btn-group" role="group">
			<button type="button" class="btn btn-sm btn-outline-primary active">Day</button>
			<button type="button" class="btn btn-sm btn-outline-primary">Week</button>
			<button type="button" class="btn btn-sm btn-outline-primary">Month</button>
		  </div>
		</div>
	  </div>

	  <div class="card border-0 shadow-sm mb-4">
		<div class="card-body p-0">
		  <div class="table-responsive">
			<table class="table table-hover align-middle mb-0">
			  <thead class="bg-light">
				<tr>
				  <th class="ps-4">Attendee</th>
				  <th>Event Type</th>
				  <th>Date & Time</th>
				  <th>Duration</th>
				  <th class="pe-4">Actions</th>
				</tr>
			  </thead>
			  <tbody>
				@forelse ($upcomingMeetings as $upcomingMeeting)
				  {{ $upcomingMeetings->count() }}
				  <tr>
					<td class="ps-4">
					  <div class="d-flex align-items-center">
						<img
						  src="https://ui-avatars.com/api/?name={{ $upcomingMeeting->attendee_name }}&background=4f46e5&color=fff"
						  class="rounded-circle me-3" width="42" height="42"
						  alt="{{ $upcomingMeeting->attendee_name }}">
						<div>
						  <h6 class="mb-0 fw-semibold">
							{{ $upcomingMeeting->attendee_name }}</h6>
						  <span class="text-muted small">{{ $upcomingMeeting->attendee_email }}</span>
						</div>
					  </div>
					</td>
					<td>
					  {{ $upcomingMeeting->eventType?->name ?? 'N/A' }}
					</td>

					<td>
					  <div>
						{{ \Carbon\Carbon::parse($upcomingMeeting->start_time)->format('l, g:i A') }}

					  </div>
					  <span class="badge bg-warning text-dark">
						{{ \Carbon\Carbon::parse($upcomingMeeting->start_time)->diffForHumans() }}
					  </span>
					</td>
					<td>
					  {{ $upcomingMeeting->eventType?->duration ?? 'N/A' }} min
					</td>

					<td class="pe-4">
					  <div class="d-flex">
						<button class="btn btn-sm btn-light me-2" title="Reschedule"><i
							class="bi bi-calendar"></i></button>
						<button class="btn btn-sm btn-light me-2" title="Cancel"><i class="bi bi-x-lg"></i></button>
						<div class="dropdown">
						  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
							<i class="bi bi-three-dots-vertical"></i>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item" href="#"><i class="bi bi-clipboard me-2"></i>Copy
								Invite
								Link</a></li>
							<li><a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i>Email
								Attendee</a></li>
							<li>
							  <hr class="dropdown-divider">
							</li>
							<li><a class="dropdown-item text-danger" href="#"><i
								  class="bi bi-trash me-2"></i>Cancel
								Meeting</a>
							</li>
						  </ul>
						</div>
					  </div>
					</td>
				  </tr>
				@empty
				  <tr>
					<td colspan="5" class="text-center">No upcoming meetings</td>
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
			  <h5 class="mb-0 fw-semibold">September 2023</h5>
			  <div>
				<button class="btn btn-sm btn-primary bg-opacity-75 border-0 me-2"><i
					class="bi bi-chevron-left"></i></button>
				<button class="btn btn-sm btn-primary bg-opacity-75 border-0"><i
					class="bi bi-chevron-right"></i></button>
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
			<div class="row g-0">
			  <div class="col calendar-day disabled">
				<div class="p-2">
				  <div class="day-number mb-2">28</div>
				</div>
			  </div>
			  <div class="col calendar-day disabled">
				<div class="p-2">
				  <div class="day-number mb-2">29</div>
				</div>
			  </div>
			  <div class="col calendar-day disabled">
				<div class="p-2">
				  <div class="day-number mb-2">30</div>
				</div>
			  </div>
			  <div class="col calendar-day disabled">
				<div class="p-2">
				  <div class="day-number mb-2">31</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">1</div>
				  <div class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
					2 events</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">2</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">3</div>
				</div>
			  </div>
			</div>
			<div class="row g-0">
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">4</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">5</div>
				  <div class="small py-1 px-2 bg-success text-white rounded mb-1 text-truncate">
					1 event</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">6</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">7</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">8</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">9</div>
				  <div class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
					3 events</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">10</div>
				</div>
			  </div>
			</div>
			<div class="row g-0">
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">11</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">12</div>
				</div>
			  </div>
			  <div class="col calendar-day active">
				<div class="p-2">
				  <div class="day-number mb-2">13</div>
				  <div class="small py-1 px-2 bg-warning text-dark rounded mb-1 text-truncate">
					Today</div>
				  <div class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
					2 events</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">14</div>
				  <div class="small py-1 px-2 bg-primary text-white rounded mb-1 text-truncate">
					1 event</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">15</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">16</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">17</div>
				</div>
			  </div>
			</div>
			<div class="row g-0">
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">18</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">19</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">20</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">21</div>
				  <div class="small py-1 px-2 bg-success text-white rounded mb-1 text-truncate">
					1 event</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">22</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">23</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">24</div>
				</div>
			  </div>
			</div>
			<div class="row g-0">
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">25</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">26</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">27</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">28</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">29</div>
				</div>
			  </div>
			  <div class="col calendar-day">
				<div class="p-2">
				  <div class="day-number mb-2">30</div>
				</div>
			  </div>
			  <div class="col calendar-day disabled">
				<div class="p-2">
				  <div class="day-number mb-2">1</div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>

	  <div class="col-lg-4 mb-4">
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
	  </div>
	</div>
  </div>
@endsection
