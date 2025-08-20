# ScheduleSync

A modern appointment scheduling system built with Laravel 12, similar to Calendly.

## Features

- **User Management** - Registration, authentication, profiles
- **Event Types** - Create different meeting types (15min, 30min, etc.)
- **Booking System** - Schedule, reschedule, cancel appointments
- **Availability Management** - Set working hours and time slots
- **Public Booking** - Share booking links with clients
- **Dashboard** - Overview of meetings and statistics
- **API First** - RESTful API with Sanctum authentication

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+, SQLite
- **Frontend:** Bootstrap 5, Blade templates
- **API:** Laravel Sanctum, API Resources
- **Testing:** Pest PHP
- **Documentation:** Scramble (API docs)

## Quick Start

```bash
# Clone and install
git clone <repository>
cd schedule_sync
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Start development
composer run dev
