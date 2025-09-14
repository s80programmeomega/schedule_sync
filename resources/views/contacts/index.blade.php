@extends('layout.base')

@section('content')
<div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Contacts</h1>
            <p class="text-muted mb-0">Manage your external contacts</p>
        </div>
        <a href="{{ route('contacts.create', ['team_id' => $teamId]) }}" class="btn btn-primary">
            <i class="bi bi-plus me-2"></i>Add Contact
        </a>
    </div>

    <!-- Filter -->
    @include('partials.filters', [
    'filters' => [
        [
            'name' => 'search',
            'type' => 'search',
            'placeholder' => 'Search name, email, company, job title...',
            'width' => 3
        ],
        [
            'name' => 'team_id',
            'type' => 'select',
            'placeholder' => 'Personal Contacts',
            'options' => $teams->pluck('name', 'id')->toArray(),
            'width' => 2
        ],
        [
            'name' => 'company',
            'type' => 'select',
            'placeholder' => 'All Companies',
            'options' => $companies->toArray(),
            'width' => 2
        ],
        [
            'name' => 'job_title',
            'type' => 'select',
            'placeholder' => 'All Job Titles',
            'options' => $jobTitles->toArray(),
            'width' => 2
        ],
        [
            'name' => 'timezone',
            'type' => 'select',
            'placeholder' => 'All Timezones',
            'options' => $timezones->pluck('display_name', 'id')->toArray(),
            'width' => 2
        ],
        [
            'name' => 'email_notifications',
            'type' => 'select',
            'placeholder' => 'Email Notifications',
            'options' => ['yes' => 'Enabled', 'no' => 'Disabled'],
            'width' => 2
        ],
        [
            'name' => 'total_bookings_min',
            'type' => 'search',
            'placeholder' => 'Min bookings',
            'width' => 1
        ],
        [
            'name' => 'last_contacted',
            'type' => 'select',
            'placeholder' => 'Last Contacted',
            'options' => [
                '7' => 'Last 7 days',
                '30' => 'Last 30 days',
                '90' => 'Last 90 days'
            ],
            'width' => 2
        ]
    ]
])




    <!-- Contacts List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Contact</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Bookings</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $contact->avatar_url }}" class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">{{ $contact->name }}</h6>
                                        <small class="text-muted">{{ $contact->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-medium">{{ $contact->company ?? '-' }}</span>
                                    @if($contact->job_title)
                                    <br><small class="text-muted">{{ $contact->job_title }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $contact->phone ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $contact->total_bookings }}</span>
                            </td>
                            <td class="pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('contacts.show', $contact) }}">View</a></li>
                                        <li><a class="dropdown-item" href="{{ route('contacts.edit', $contact) }}">Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Archive</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 py-4">No contacts found!</h5>
                                <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-2"></i> Add Contact
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($contacts->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $contacts->links() }}
    </div>
    @endif
</div>
@endsection
