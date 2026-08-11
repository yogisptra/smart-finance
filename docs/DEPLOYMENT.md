# Deployment Guide

## Prerequisites
- Docker & Docker Compose
- PHP 8.2+
- Composer
- Node.js

## Backend (Laravel) Deployment
1. Copy `.env.example` to `.env` and configure `DB_*` and `REDIS_*`.
2. Run `composer install --optimize-autoloader --no-dev`.
3. Start infrastructure: `docker compose up -d` (Postgres + Redis).
4. Run migrations: `php artisan migrate --force`.
5. Optimize Laravel: `php artisan optimize`.
6. Start background workers for OCR: `php artisan queue:work redis --tries=3`.

## Frontend (React Native) Deployment
- For Android: `./gradlew assembleRelease` or `./gradlew bundleRelease` for Play Store.
- Make sure to point `API_URL` to your production host in `.env` before building.
- Set up proper keystores for code signing.
