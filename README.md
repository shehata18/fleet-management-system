# Bus Booking System API

A fleet management system built with Laravel that handles bus seat bookings between stations. The system allows users to book seats for specific trip segments while ensuring no double bookings occur for overlapping segments.

## System Overview

The system manages bus trips between Egyptian cities (stations) where:
- Each trip has multiple stations (e.g., Cairo → AlFayyum → AlMinya → Asyut)
- Each bus has 12 available seats
- Users can book seats for any segment of the trip
- System prevents double booking of seats for overlapping segments

## Technical Stack

- PHP 8.1+
- Laravel 11
- MySQL 8.0+
- Laravel Sanctum for Authentication

## Installation

1. Clone the repository and install dependencies:

```bash
git clone https://github.com/shehata18/fleet-management-system.git
cd fleet-management-system
composer install
```
for laravel sanctum you need to run this command to install the package

```bash
composer require laravel/sanctum
```
and then you need to run this command to publish the migrations


2. Create a `.env` file and configure the database:

```bash
cp .env.example .env
```
you can copy the .env.example file and paste it in the root of the project and name it .env

3. Run the migrations and seed the database:

```bash
php artisan migrate:fresh --seed
```

4. Start the Laravel development server:

```bash
php artisan serve
```

## Database Seeded Data

The system comes pre-seeded with:

### Stations
- Cairo
- Giza
- AlFayyum
- AlMinya
- Asyut

### Bus
- One bus (BUS-001) with 12 seats (SEAT-01 to SEAT-12)

### Trip
- Cairo-Asyut Trip with stops:
  1. Cairo
  2. AlFayyum
  3. AlMinya
  4. Asyut

## API Endpoints

### Authentication

```bash:README.md
# Register
POST /api/register
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
}

# Login
POST /api/login
{
    "email": "test@example.com",
    "password": "password"
}

# Logout (Authenticated)
POST /api/logout
```

### Stations
```bash
# List all stations
GET /api/stations

# Get station details
GET /api/stations/{id}
```

### Trips
```bash
# List all trips
GET /api/trips

# Get trip details
GET /api/trips/{id}
```

### Bookings
```bash
# Check available seats
GET /api/available-seats?trip_id=1&from_station_id=1&to_station_id=3

# Create booking
POST /api/bookings
{
    "trip_id": 1,
    "from_station_id": 1,
    "to_station_id": 3,
    "seat_id": 1
}

# List user's bookings
GET /api/my-bookings

# Get booking details
GET /api/bookings/{id}

# Cancel booking
POST /api/bookings/{id}/cancel
```

## Testing the API

### Using Postman
1. Import the provided collection from `postman/bus_booking.json`
2. Set up environment variables:
   - `base_url`: `http://localhost:8000`
   - `token`: Will be set after login

or you can use the following link to view the documentation

[API Documentation](https://documenter.getpostman.com/view/23602722/2sAYX6q2UY) 


### Example Flow
1. Register/Login to get authentication token
2. List available trips
3. Check available seats for your journey
4. Create a booking
5. View or cancel your booking

## Booking Rules

1. Users can book seats for any segment of a trip
2. System checks for seat availability considering:
   - Existing bookings for the exact segment
   - Overlapping bookings (e.g., if seat is booked for Cairo→AlMinya, it can't be booked for AlFayyum→Asyut)
   - Station sequence validation
3. Users can only view and cancel their own bookings

## Error Responses

```json
{
    "status": "error",
    "message": "Error description"
}
```

Common error codes:
- 401: Unauthorized
- 403: Forbidden (trying to access/modify other user's booking)
- 404: Resource not found
- 422: Validation error (invalid input)
- 500: Server error

## Development Notes

- All API responses are JSON formatted
- Authentication uses Laravel Sanctum tokens
- Proper validation is implemented for all inputs
- Database transactions ensure booking integrity
- Comprehensive error handling is implemented
