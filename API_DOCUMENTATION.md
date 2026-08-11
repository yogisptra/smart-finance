# API Documentation

Base URL: `/api/v1`

## Authentication
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/logout`
- `GET /auth/me`
- `PUT /auth/profile`
- `PUT /auth/password`

## Transactions
- `GET /transactions`
- `POST /transactions`
- `GET /transactions/{id}`
- `PUT /transactions/{id}`
- `DELETE /transactions/{id}`

## Receipts
- `POST /receipts`
- `GET /receipts/{id}`
- `GET /receipts/{id}/status`
- `GET /receipts/{id}/image`
- `POST /receipts/{id}/process`
- `POST /receipts/{id}/confirm`
- `POST /receipts/{id}/retry`
- `DELETE /receipts/{id}`

## Budgets
- `GET /budgets`
- `POST /budgets`
- `GET /budgets/{id}`
- `PUT /budgets/{id}`
- `DELETE /budgets/{id}`

## Categories & Payment Methods
- `GET /categories`
- `GET /payment-methods`

## Reports
- `GET /reports/summary`
- `GET /reports/category`
- `GET /reports/trend`
