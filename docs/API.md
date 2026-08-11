# API Contract

All endpoints reside under `/api/v1`.
All requests and responses use JSON.

## Standard Response Format
```json
{
    "success": true,
    "message": "Action successful",
    "data": {}
}
```

## Pagination Format
```json
{
    "success": true,
    "message": "List retrieved",
    "data": [],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 100,
        "last_page": 5
    }
}
```

## Authentication
`POST /auth/register`
`POST /auth/login`
`POST /auth/logout` (Requires Bearer Token)
`GET /auth/me` (Requires Bearer Token)

## Transactions
`GET /transactions`
`POST /transactions`
`GET /transactions/{id}`
`PUT /transactions/{id}`
`DELETE /transactions/{id}`

Example POST Payload:
```json
{
    "type": "expense",
    "amount": 50000,
    "category_id": 1,
    "payment_method_id": 2,
    "merchant_name": "Warung",
    "transaction_date": "2026-08-11"
}
```

## Receipts
`POST /receipts` (multipart/form-data with `image`)
`GET /receipts/{id}`
`GET /receipts/{id}/status`
`GET /receipts/{id}/image`
`POST /receipts/{id}/confirm`

## Categories
`GET /categories`
`POST /categories`

## Payment Methods
`GET /payment-methods`
`POST /payment-methods`

## Budgets
`GET /budgets`
`POST /budgets`
