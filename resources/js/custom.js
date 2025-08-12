// Show tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Time slot selection
const timeSlots = document.querySelectorAll('.time-slot');
timeSlots.forEach(slot => {
    slot.addEventListener('click', function (e) {
        e.preventDefault();
        timeSlots.forEach(s => s.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// Create event button - show modal
document.querySelector('#createEventBtn').addEventListener('click', function () {
    const createEventModal = new bootstrap.Modal(document.getElementById('createEventModal'));
    createEventModal.show();
});

// Calendar day hover effect
const calendarDays = document.querySelectorAll('.calendar-day:not(.disabled)');
calendarDays.forEach(day => {
    day.addEventListener('click', function () {
        calendarDays.forEach(d => d.classList.remove('active'));
        this.classList.add('active');
    });
});
