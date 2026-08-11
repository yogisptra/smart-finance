# Technical Architecture

## 1. System Overview
The Smart Finance application uses a client-server architecture consisting of a React Native mobile application and a Laravel 11/12 API backend.

## 2. Tech Stack
- **Frontend**: React Native, TypeScript, Zustand (State Management), React Query (Data Fetching), Axios, React Navigation.
- **Backend**: Laravel 11/12, PHP 8.2+.
- **Database**: PostgreSQL for relational data.
- **Queue/Cache**: Redis.

## 3. Design Patterns
- **Backend**: 
  - Controllers strictly handle HTTP concerns (requests & responses).
  - Business logic resides in `app/Services/`.
  - Data transfer utilizes `App\DTOs`.
  - API resources (`App\Http\Resources`) format JSON responses.
  - Form Requests handle validation.
- **Frontend**: 
  - Centralized API client with interceptors.
  - Zustand for Auth/Preferences.
  - TanStack Query for robust data fetching, caching, and invalidation.

## 4. Concurrency & Security
- Database transactions (`DB::transaction`) and pessimistic locking (`lockForUpdate`) are used where necessary (e.g., Receipt Confirmation) to prevent race conditions.
- Strict API Resource masking avoids leaking sensitive data (passwords, tokens, internal paths).
