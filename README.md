# Smart Finance Platform

Smart Finance is a personal finance mobile application that helps users track their income, expenses, and budgets. Its core feature is an AI-powered Receipt Scanner that automatically extracts transaction data from uploaded receipts.

## Features
- **Authentication**: JWT via Laravel Sanctum.
- **Dashboard**: Monthly financial summary.
- **Manual Transactions**: Categorize income & expense.
- **Receipt OCR & AI Parsing**: Automatically fill transaction details from a photo using an abstract OCR and AI pipeline.
- **Budgeting**: Set daily/weekly/monthly budgets.
- **Reporting**: Aggregate reports by category and trend.

## Tech Stack
- **Backend**: Laravel 11/12, PostgreSQL, Redis.
- **Mobile**: React Native, TypeScript, Zustand, React Query.

## Quick Start (Docker)

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/smart-finance.git
   cd smart-finance
   ```

2. **Environment Variables**
   Create a `.env` file inside `backend/`:
   ```bash
   cd backend
   cp .env.example .env
   # Update DB_PASSWORD, DB_DATABASE etc if you change the defaults.
   ```

3. **Start Infrastructure**
   Go back to the root and use Docker Compose:
   ```bash
   cd ..
   docker compose up -d
   ```

4. **Initialize Database**
   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

The API should now be running at `http://localhost:8000/api/v1`.

## Mobile Setup

1. Navigate to the `mobile` directory:
   ```bash
   cd mobile
   npm install
   ```

2. Set the `API_URL` environment variable for the local IP of your machine. For Android emulators pointing to host localhost: `http://10.0.2.2:8000/api/v1`.

3. Run the app:
   ```bash
   npx react-native run-android
   # or
   npx react-native run-ios
   ```

## Development Commands

**Backend Logs**:
```bash
docker compose logs -f app
docker compose logs -f queue
```

**Running Tests**:
```bash
docker compose exec app php artisan test
```

**Clearing Cache**:
```bash
docker compose exec app php artisan optimize:clear
```

## Architecture Documentation
See the `docs/` folder for detailed documentation on:
- [FSD (Functional Specification Document)](docs/FSD.md)
- [Architecture Overview](docs/ARCHITECTURE.md)
- [Database ERD & Rules](docs/DATABASE.md)
- [API Contract](docs/API.md)
- [OCR Flow & Configuration](docs/OCR.md)
- [Deployment Process](docs/DEPLOYMENT.md)

## Troubleshooting

- **PostgreSQL / Redis Connection Refused**: Ensure containers are running (`docker compose ps`). Check that your `.env` variables use the correct service names (`DB_HOST=postgres`, `REDIS_HOST=redis`).
- **Queue not processing**: Verify the queue worker is running via `docker compose logs -f queue`.
- **Permission Denied in Storage**: Docker container needs write access to `storage/` and `bootstrap/cache`. Run `docker compose exec app chown -R www-data:www-data /var/www/html/storage`.
- **Mobile cannot connect to localhost**: For Android physical devices, ensure `API_URL` uses your computer's local Wi-Fi IP address.

## Security & Storage Limitations
This MVP explicitly uses the local filesystem (`storage/app/receipts`) to store uploaded receipts. Images are accessible internally via the authenticated `GET /receipts/{id}/image` endpoint. Do not configure or use S3 for this stage.
