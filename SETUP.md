# Setup Instructions

## Prerequisites
- Docker & Docker Compose
- Node.js (v20+)
- Composer
- PHP 8.3+

## Backend Setup
1. `cd backend`
2. `cp .env.example .env`
3. Update `.env` with DB credentials from `docker-compose.yml`
4. `docker-compose up -d` (from root dir)
5. `composer install`
6. `php artisan key:generate`
7. `php artisan migrate --seed`
8. `php artisan storage:link`
9. `php artisan serve`

## Mobile Setup
1. `cd mobile`
2. `npm install`
3. Update `.env` with your API URL
4. `npm run android` or `npm run ios`
