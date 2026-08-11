# Database Specification

## ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ TRANSACTIONS : owns
    USERS ||--o{ RECEIPTS : owns
    USERS ||--o{ CATEGORIES : owns
    USERS ||--o{ PAYMENT_METHODS : owns
    USERS ||--o{ BUDGETS : owns

    TRANSACTIONS ||--o{ TRANSACTION_ITEMS : contains
    TRANSACTIONS ||--o| RECEIPTS : has

    RECEIPTS ||--o{ RECEIPT_ITEMS : contains
    RECEIPTS ||--o| RECEIPT_OCR_RESULTS : produces

    CATEGORIES ||--o{ TRANSACTIONS : categorizes
    PAYMENT_METHODS ||--o{ TRANSACTIONS : uses
    
    USERS {
        id bigint PK
        name varchar
        email varchar
        password varchar
        currency varchar
        timezone varchar
    }
    
    TRANSACTIONS {
        id bigint PK
        user_id bigint FK
        category_id bigint FK
        payment_method_id bigint FK
        receipt_id bigint FK
        type varchar
        amount decimal
        currency varchar
        merchant_name varchar
        transaction_date date
        status varchar
    }
    
    RECEIPTS {
        id bigint PK
        user_id bigint FK
        file_name varchar
        file_path varchar
        status varchar
    }
    
    CATEGORIES {
        id bigint PK
        user_id bigint FK
        name varchar
        type varchar
        is_default boolean
    }
```

## Conventions
- **Ownership**: Every user-owned record must include `user_id`.
- **Soft Deletes**: Used across user-facing entities (Transactions, Categories, Budgets, Receipts) to allow recovery and preserve history.
- **Audit Logs**: Immutable records of every CUD action on critical entities.
