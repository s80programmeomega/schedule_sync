{{-- Enhanced booking creation with comprehensive member import --}}
@extends('layout.base')

@section('content')
  <div class="col-lg-10 col-12 py-4 px-4 px-lg-5">
    {{-- Display all validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <h6 class="alert-heading mb-2">Please correct the following errors:</h6>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="mb-4">
      <h1 class="h3 mb-0 fw-bold">Create Booking</h1>
      <p class="text-muted mb-0">Schedule a meeting with multiple attendees</p>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <form method="POST" action="{{ route('bookings.store-with-attendees') }}" id="bookingForm">
              @csrf

              <div class="mb-4">
                <label for="event_type_id" class="form-label fw-semibold">Event Type</label>
                <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id"
                  name="event_type_id" required>
                  <option value="">Select Event Type</option>
                  @foreach ($eventTypes as $eventType)
                    <option value="{{ $eventType->id }}" data-duration="{{ $eventType->duration }}"
                      data-multiple="{{ $eventType->allow_multiple_attendees ? 'true' : 'false' }}">
                      {{ $eventType->name }} ({{ $eventType->duration }} min)
                    </option>
                  @endforeach
                </select>
                @error('event_type_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="booking_date" class="form-label fw-semibold">Date</label>
                  <input type="date" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date"
                    name="booking_date" value="{{ old('booking_date') }}" required>
                  @error('booking_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="start_time" class="form-label fw-semibold">Start Time</label>
                  <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                    name="start_time" value="{{ old('start_time') }}" required>
                  @error('start_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="mb-3">
                <label for="timezone" class="form-label fw-semibold">Timezone</label>
                <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                  <option value="">Select Timezone</option>
                  @if (isset($timezones))
                    @foreach ($timezones as $timezone)
                      <option value="{{ $timezone->name }}" {{ old('timezone') == $timezone->name ? 'selected' : '' }}>
                        {{ $timezone->display_name }}
                      </option>
                    @endforeach
                  @endif
                </select>
                @error('timezone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Status Field -->
              <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                  <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled
                  </option>
                  <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                  <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                  <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Meeting Link Field -->
              <div class="mb-3">
                <label for="meeting_link" class="form-label fw-semibold">Meeting Link</label>
                <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" id="meeting_link"
                  name="meeting_link" value="{{ old('meeting_link') }}" placeholder="https://zoom.us/j/123456789">
                @error('meeting_link')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>


              <!-- Enhanced Import Attendees Section -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <label class="form-label fw-semibold mb-0">Import Attendees</label>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="showImportModal('team')">
                      <i class="bi bi-people me-1"></i>From Team
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="showImportModal('group')">
                      <i class="bi bi-collection me-1"></i>From Group
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="showImportModal('contacts')">
                      <i class="bi bi-person-lines-fill me-1"></i>All Contacts
                    </button>
                  </div>
                </div>
              </div>

              <!-- Attendees Section -->
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <label class="form-label fw-semibold mb-0">Attendees</label>
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAttendee()">
                    <i class="bi bi-plus me-1"></i>Add Individual
                  </button>
                </div>

                <div id="attendees-container">
                  <!-- Selected attendees will appear here -->
                </div>
              </div>

              {{-- Add validation errors for attendees --}}
              @if ($errors->has('attendees'))
                <div class="alert alert-danger mt-2">
                  <ul class="mb-0">
                    @foreach ($errors->get('attendees.*') as $fieldErrors)
                      @foreach ($fieldErrors as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check me-2"></i>Create Booking
                </button>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Enhanced Import Modal -->
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalTitle">Import Attendees</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Search and Filter Section -->
          <div class="mb-3" id="searchSection" style="display: none;">
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="memberSearch" placeholder="Search by name or email...">
              <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                <i class="bi bi-x"></i>
              </button>
            </div>
          </div>

          <div id="importContent">
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>
        <div class="modal-footer">
          <div class="d-flex justify-content-between w-100">
            <div>
              <span id="selectionCount" class="text-muted">0 selected</span>
            </div>
            <div>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="importSelected()" id="importBtn" disabled>
                Import Selected
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let attendeeCount = 0;
    let currentImportType = '';
    let allMembers = [];
    let filteredMembers = [];

    /**
     * Enhanced import modal with support for teams, groups, and all contacts
     *
     * @param {string} type - 'team', 'group', or 'contacts'
     */
    function showImportModal(type) {
      const modal = new bootstrap.Modal(document.getElementById('importModal'));
      const title = document.getElementById('importModalTitle');
      const content = document.getElementById('importContent');
      const searchSection = document.getElementById('searchSection');

      currentImportType = type;

      // Set modal title and show search for contacts
      switch (type) {
        case 'team':
          title.textContent = 'Import from Team';
          searchSection.style.display = 'none';
          loadTeamSelection(content);
          break;
        case 'group':
          title.textContent = 'Import from Group';
          searchSection.style.display = 'none';
          loadGroupSelection(content);
          break;
        case 'contacts':
          title.textContent = 'Import from All Contacts';
          searchSection.style.display = 'block';
          loadAllContacts(content);
          break;
      }

      // Reset search and selection
      document.getElementById('memberSearch').value = '';
      updateSelectionCount();

      modal.show();
    }

    /**
     * Load team selection interface with enhanced options
     */
    function loadTeamSelection(container) {
      const teams = @json($teams ?? []);

      if (teams.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                You are not a member of any teams yet.
            </div>
        `;
        return;
      }

      let html = `
        <div class="mb-3">
            <label class="form-label fw-semibold">Select Team:</label>
            <select class="form-select" id="teamSelect" onchange="loadTeamMembersList(this.value)">
                <option value="">Choose a team...</option>
    `;

      teams.forEach(team => {
        html += `<option value="${team.id}">${team.name}</option>`;
      });

      html += `
            </select>
        </div>
        <div class="mb-3" id="teamOptions" style="display: none;">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="importEntireTeam()">
                    <i class="bi bi-people me-1"></i>Import Entire Team
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showTeamMemberSelection()">
                    <i class="bi bi-person-check me-1"></i>Select Specific Members
                </button>
            </div>
        </div>
        <div id="membersList"></div>
    `;

      container.innerHTML = html;
    }

    /**
     * Load group selection interface with enhanced options
     */
    function loadGroupSelection(container) {
      const groups = @json($groups ?? []);

      if (groups.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No groups available.
            </div>
        `;
        return;
      }

      let html = `
        <div class="mb-3">
            <label class="form-label fw-semibold">Select Group:</label>
            <select class="form-select" id="groupSelect" onchange="loadGroupMembersList(this.value)">
                <option value="">Choose a group...</option>
    `;

      groups.forEach(group => {
        html += `<option value="${group.id}">${group.name}</option>`;
      });

      html += `
            </select>
        </div>
        <div class="mb-3" id="groupOptions" style="display: none;">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="importEntireGroup()">
                    <i class="bi bi-collection me-1"></i>Import Entire Group
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showGroupMemberSelection()">
                    <i class="bi bi-person-check me-1"></i>Select Specific Members
                </button>
            </div>
        </div>
        <div id="membersList"></div>
    `;

      container.innerHTML = html;
    }

    /**
     * Load all contacts with search functionality
     */
    function loadAllContacts(container) {
      container.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <div class="mt-2">Loading all contacts...</div>
        </div>
    `;

      // Fix CSRF token access
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      if (!csrfToken) {
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                CSRF token not found. Please refresh the page.
            </div>
        `;
        return;
      }

      fetch('/contacts/all-json', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content
          },
          credentials: 'same-origin'
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          allMembers = data.contacts.map(contact => ({
            id: contact.id,
            name: contact.name,
            email: contact.email,
            type: 'contact',
            company: contact.company || '',
            job_title: contact.job_title || ''
          }));
          filteredMembers = [...allMembers];
          displayContactsList(filteredMembers);

          // Enable search functionality with null check
          const searchInput = document.getElementById('memberSearch');
          if (searchInput) {
            searchInput.addEventListener('input', filterContacts);
          }
        })
        .catch(error => {
          console.error('Error loading contacts:', error);
          container.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed to load contacts. Please try again.
            </div>
        `;
        });
    }


    /**
     * Enhanced team members loading with bulk options
     */
    function loadTeamMembersList(teamId) {
      if (!teamId) {
        document.getElementById('membersList').innerHTML = '';
        document.getElementById('teamOptions').style.display = 'none';
        return;
      }

      document.getElementById('teamOptions').style.display = 'block';
      document.getElementById('membersList').innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <div class="mt-2">Loading team members...</div>
        </div>
    `;

      fetch(`/teams/${teamId}/members-json`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          credentials: 'same-origin'
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          // Fix: Ensure data.members exists and is an array
          allMembers = Array.isArray(data.members) ? data.members : [];
          filteredMembers = [...allMembers];
          document.getElementById('membersList').innerHTML = '';
        })
        .catch(error => {
          console.error('Error loading team members:', error);
          document.getElementById('membersList').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed to load team members. Please try again.
            </div>
        `;
        });
    }


    /**
     * Enhanced group members loading with bulk options
     */
    // Fix the loadGroupMembersList function
    function loadGroupMembersList(groupId) {
      if (!groupId) {
        document.getElementById('membersList').innerHTML = '';
        document.getElementById('groupOptions').style.display = 'none';
        return;
      }

      document.getElementById('groupOptions').style.display = 'block';
      document.getElementById('membersList').innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <div class="mt-2">Loading group members...</div>
        </div>
    `;

      fetch(`/groups/${groupId}/members-json`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          credentials: 'same-origin'
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          // Fix: Ensure data.members exists and is an array
          allMembers = Array.isArray(data.members) ? data.members : [];
          filteredMembers = [...allMembers];
          document.getElementById('membersList').innerHTML = '';
        })
        .catch(error => {
          console.error('Error loading group members:', error);
          document.getElementById('membersList').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failed to load group members. Please try again.
            </div>
        `;
        });
    }


    /**
     * Import entire team without selection
     */
    function importEntireTeam() {
      if (allMembers.length === 0) {
        showNotification('No team members to import', 'warning');
        return;
      }

      let importedCount = 0;
      allMembers.forEach(member => {
        const name = member.user ? member.user.name : member.name;
        const email = member.user ? member.user.email : member.email;
        addAttendeeRow(name, email, 'required', 'team', member.id);
        importedCount++;
      });

      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
      showNotification(`Successfully imported ${importedCount} team members`, 'success');
    }

    /**
     * Import entire group without selection
     */
    function importEntireGroup() {
      if (allMembers.length === 0) {
        showNotification('No group members to import', 'warning');
        return;
      }

      let importedCount = 0;
      allMembers.forEach(member => {
        addAttendeeRow(member.name, member.email, 'required', 'group', member.member_id || member.id);
        importedCount++;
      });

      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
      showNotification(`Successfully imported ${importedCount} group members`, 'success');
    }

    /**
     * Show team member selection interface
     */
    function showTeamMemberSelection() {
      displayMembersList(filteredMembers, 'team');
    }

    /**
     * Show group member selection interface
     */
    function showGroupMemberSelection() {
      displayMembersList(filteredMembers, 'group');
    }

    /**
     * Enhanced member list display with better UI
     */
    function displayMembersList(members, type) {
      const container = document.getElementById('membersList');

      if (!members || members.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No members found.
            </div>
        `;
        return;
      }

      let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-semibold">${members.length} member${members.length !== 1 ? 's' : ''}</span>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllMembers(true)">
                    <i class="bi bi-check-all me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllMembers(false)">
                    <i class="bi bi-x me-1"></i>Clear All
                </button>
            </div>
        </div>
        <div class="border rounded p-2" style="max-height: 400px; overflow-y: auto;">
    `;

      members.forEach(member => {
        const name = member.user ? member.user.name : member.name;
        const email = member.user ? member.user.email : member.email;
        const memberId = member.member_id || member.id;
        const role = member.role || '';
        const company = member.company || '';
        const jobTitle = member.job_title || '';

        html += `
            <div class="form-check p-3 border-bottom member-item" data-name="${name.toLowerCase()}" data-email="${email.toLowerCase()}">
                <input class="form-check-input" type="checkbox" value="${memberId}"
                       id="member_${memberId}"
                       data-name="${name}"
                       data-email="${email}"
                       data-type="${type}"
                       data-role="${role}"
                       onchange="updateSelectionCount()">
                <label class="form-check-label d-flex justify-content-between align-items-center w-100"
                       for="member_${memberId}">
                    <div class="flex-grow-1">
                        <div class="fw-medium">${name}</div>
                        <small class="text-muted">${email}</small>
                        ${company || jobTitle ? `<div><small class="text-muted">${jobTitle}${company ? ` at ${company}` : ''}</small></div>` : ''}
                    </div>
                    ${role ? `<span class="badge bg-secondary ms-2">${role}</span>` : ''}
                </label>
            </div>
        `;
      });

      html += '</div>';
      container.innerHTML = html;
      updateSelectionCount();
    }

    /**
     * Enhanced contacts display
     */
    function displayContactsList(contacts) {
      const container = document.getElementById('importContent');

      if (!contacts || contacts.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No contacts found.
            </div>
        `;
        return;
      }

      let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-semibold">${contacts.length} contact${contacts.length !== 1 ? 's' : ''}</span>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllContacts(true)">
                    <i class="bi bi-check-all me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllContacts(false)">
                    <i class="bi bi-x me-1"></i>Clear All
                </button>
            </div>
        </div>
        <div class="border rounded p-2" style="max-height: 400px; overflow-y: auto;">
    `;

      contacts.forEach(contact => {
        html += `
            <div class="form-check p-3 border-bottom contact-item" data-name="${contact.name.toLowerCase()}" data-email="${contact.email.toLowerCase()}">
                <input class="form-check-input" type="checkbox" value="${contact.id}"
                       id="contact_${contact.id}"
                       data-name="${contact.name}"
                       data-email="${contact.email}"
                       data-type="contact"
                       onchange="updateSelectionCount()">
                <label class="form-check-label d-flex justify-content-between align-items-center w-100"
                       for="contact_${contact.id}">
                    <div class="flex-grow-1">
                        <div class="fw-medium">${contact.name}</div>
                        <small class="text-muted">${contact.email}</small>
                        ${contact.company || contact.job_title ? `<div><small class="text-muted">${contact.job_title}${contact.company ? ` at ${contact.company}` : ''}</small></div>` : ''}
                    </div>
                </label>
            </div>
        `;
      });

      html += '</div>';
      container.innerHTML = html;
      updateSelectionCount();
    }

    /**
     * Filter contacts based on search input
     */
    function filterContacts() {
      const searchTerm = document.getElementById('memberSearch').value.toLowerCase();

      if (searchTerm === '') {
        filteredMembers = [...allMembers];
      } else {
        filteredMembers = allMembers.filter(member =>
          member.name.toLowerCase().includes(searchTerm) ||
          member.email.toLowerCase().includes(searchTerm) ||
          (member.company && member.company.toLowerCase().includes(searchTerm)) ||
          (member.job_title && member.job_title.toLowerCase().includes(searchTerm))
        );
      }

      displayContactsList(filteredMembers);
    }

    /**
     * Clear search input
     */
    function clearSearch() {
      document.getElementById('memberSearch').value = '';
      filterContacts();
    }

    /**
     * Select/deselect all members
     */
    function selectAllMembers(selectAll) {
      const checkboxes = document.querySelectorAll('#membersList input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll;
      });
      updateSelectionCount();
    }

    /**
     * Select/deselect all contacts
     */
    function selectAllContacts(selectAll) {
      const checkboxes = document.querySelectorAll('#importContent input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll;
      });
      updateSelectionCount();
    }

    /**
     * Update selection count and import button state
     */
    function updateSelectionCount() {
      const checkboxes = document.querySelectorAll('#importModal input[type="checkbox"]:checked');
      const count = checkboxes.length;
      const countElement = document.getElementById('selectionCount');
      const importBtn = document.getElementById('importBtn');

      countElement.textContent = `${count} selected`;
      importBtn.disabled = count === 0;
    }

    /**
     * Enhanced import selected with better feedback
     */
    function importSelected() {
      const checkboxes = document.querySelectorAll('#importModal input[type="checkbox"]:checked');

      if (checkboxes.length === 0) {
        showNotification('Please select at least one member to import', 'warning');
        return;
      }

      let importedCount = 0;

      Array.from(checkboxes).reverse().forEach(checkbox => {
        const name = checkbox.dataset.name;
        const email = checkbox.dataset.email;
        const type = checkbox.dataset.type;
        const memberId = checkbox.value;

        addAttendeeRow(name, email, 'required', type, memberId);
        importedCount++;
      });


      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
      showNotification(`Successfully imported ${importedCount} attendee${importedCount !== 1 ? 's' : ''}`, 'success');
    }

    /**
     * Add individual attendee manually
     */
    function addAttendee() {
      addAttendeeRow('', '', 'required', 'email', '');
    }

    /**
     * Enhanced attendee row creation
     */
    function addAttendeeRow(name = '', email = '', role = 'required', type = 'email', memberId = '') {
      const container = document.getElementById('attendees-container');
      const row = document.createElement('div');
      row.className = 'attendee-row border rounded p-3 mb-3';

      const isImported = name && email;

      // For imported contacts, we need to find the contact_id
      let contactId = '';
      if (type === 'contact' && isImported) {
        // Find contact ID from the contacts data
        const contacts = @json($contacts ?? []);
        const foundContact = contacts.find(c => c.email === email);
        contactId = foundContact ? foundContact.id : '';
      }

      row.innerHTML = `
    <div class="row">
        <div class="col-md-3 mb-2">
            <select class="form-select" name="attendees[${attendeeCount}][type]" onchange="toggleAttendeeFields(this, ${attendeeCount})" ${isImported ? 'disabled' : ''}>
                <option value="email" ${type === 'email' ? 'selected' : ''}>Enter Email</option>
                <option value="contact" ${type === 'contact' ? 'selected' : ''}>From Contacts</option>
                <option value="team" ${type === 'team' ? 'selected' : ''}>Team Member</option>
                <option value="group" ${type === 'group' ? 'selected' : ''}>Group Member</option>
            </select>
            ${isImported ? `<input type="hidden" name="attendees[${attendeeCount}][type]" value="${type}">` : ''}
        </div>
        <div class="col-md-4 mb-2">
            ${type === 'contact' ? `
                  <select class="form-select contact-select" name="attendees[${attendeeCount}][contact_id]" ${isImported ? 'disabled' : ''}>
                      <option value="">Select Contact</option>
                      @foreach ($contacts as $contact)
                      <option value="{{ $contact->id }}" ${contactId == '{{ $contact->id }}' ? 'selected' : ''}>{{ $contact->name }} ({{ $contact->email }})</option>
                      @endforeach
                  </select>
                  ${isImported && contactId ? `<input type="hidden" name="attendees[${attendeeCount}][contact_id]" value="${contactId}">` : ''}
              ` : `
                  <input type="text" class="form-control name-input"
                         name="attendees[${attendeeCount}][name]" placeholder="Full Name" value="${name}"
                         ${isImported ? 'readonly' : ''} ${type === 'email' ? 'required' : ''}>
              `}
            ${type === 'team' && memberId ? `<input type="hidden" name="attendees[${attendeeCount}][member_id]" value="${memberId}">` : ''}
            ${type === 'group' && memberId ? `<input type="hidden" name="attendees[${attendeeCount}][member_id]" value="${memberId}">` : ''}
        </div>
        <div class="col-md-3 mb-2">
            ${type !== 'contact' ? `
                  <input type="email" class="form-control email-input"
                         name="attendees[${attendeeCount}][email]" placeholder="Email Address" value="${email}"
                         ${isImported ? 'readonly' : ''} ${type === 'email' ? 'required' : ''}>
              ` : ''}
        </div>
        <div class="col-md-1 mb-2">
            <select class="form-select" name="attendees[${attendeeCount}][role]">
                <option value="required" ${role === 'required' ? 'selected' : ''}>Required</option>
                <option value="optional" ${role === 'optional' ? 'selected' : ''}>Optional</option>
                <option value="organizer" ${role === 'organizer' ? 'selected' : ''}>Organizer</option>
            </select>
        </div>
        <div class="col-md-1 mb-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeAttendee(this)" title="Remove attendee">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
  `;

      container.appendChild(row);
      attendeeCount++;
    }





    /**
     * Remove attendee row
     */
    function removeAttendee(button) {
      const row = button.closest('.attendee-row');
      row.remove();
    }

    /**
     * Toggle attendee input fields based on type
     */
    function toggleAttendeeFields(select, index) {
      if (select.disabled) return;

      const row = select.closest('.attendee-row');
      const currentRow = row.innerHTML;

      // Get current values
      const nameInput = row.querySelector('input[name*="[name]"]');
      const emailInput = row.querySelector('input[name*="[email]"]');
      const roleSelect = row.querySelector('select[name*="[role]"]');

      const currentName = nameInput ? nameInput.value : '';
      const currentEmail = emailInput ? emailInput.value : '';
      const currentRole = roleSelect ? roleSelect.value : 'required';

      // Remove the row and recreate it with new type
      const container = row.parentNode;
      const rowIndex = Array.from(container.children).indexOf(row);
      row.remove();

      // Recreate with new type
      addAttendeeRowAtIndex(currentName, currentEmail, currentRole, select.value, '', rowIndex);
    }

    function addAttendeeRowAtIndex(name, email, role, type, memberId, index) {
      // This is a simplified version - you may need to adjust based on your needs
      addAttendeeRow(name, email, role, type, memberId);
    }


    /**
     * Enhanced notification system
     */
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className =
        `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

      document.body.appendChild(notification);

      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 5000);
    }

    // Initialize form when page loads
    document.addEventListener('DOMContentLoaded', function() {
      if (document.querySelectorAll('.attendee-row').length === 0) {
        addAttendee();
      }
    });
  </script>
@endsection
