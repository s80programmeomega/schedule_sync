
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
// document.querySelector('#createEventBtn').addEventListener('click', function () {
//     const createEventModal = new bootstrap.Modal(document.getElementById('createEventModal'));
//     createEventModal.show();
// });

// Calendar day hover effect
const calendarDays = document.querySelectorAll('.calendar-day:not(.disabled)');
calendarDays.forEach(day => {
    day.addEventListener('click', function () {
        calendarDays.forEach(d => d.classList.remove('active'));
        this.classList.add('active');
    });
});

// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function () {
    if (window.innerWidth >= 992) { // Bootstrap lg breakpoint
        const sidebar = document.getElementById('sidebar');
        if (sidebar.classList.contains('d-none')) {
            sidebar.classList.remove('d-none');
            sidebar.classList.add('d-lg-block');
            } else {
                sidebar.classList.add('d-none');
                sidebar.classList.remove('d-lg-block');
            }
        }
});
// Sidebar toggle
// document.addEventListener('DOMContentLoaded', function() {
//     const toggleBtn = document.getElementById('sidebarToggle');
//     const sidebar = document.getElementById('sidebar');

//     if (toggleBtn && sidebar) {
//         toggleBtn.addEventListener('click', function() {
//             sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
//         });
//     }
// });
// Sidebar toggle
// document.addEventListener('DOMContentLoaded', function() {
//     const toggleBtn = document.getElementById('sidebarToggle');
//     const sidebar = document.getElementById('sidebar');

//     if (toggleBtn && sidebar) {
//         let isHidden = false;
//         toggleBtn.addEventListener('click', function() {
//             if (isHidden) {
//                 sidebar.classList.remove('d-none');
//                 sidebar.classList.add('d-lg-block');
//                 isHidden = false;
//             } else {
//                 sidebar.classList.remove('d-lg-block');
//                 sidebar.classList.add('d-none');
//                 isHidden = true;
//             }
//         });
//     }
// });


