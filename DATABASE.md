# Database Schema

## `users`
- id, name, email, password, currency, timezone, created_at, updated_at

## `categories`
- id, user_id, name, type (income/expense), icon, is_default, deleted_at, timestamps

## `payment_methods`
- id, user_id, name, type, deleted_at, timestamps

## `transactions`
- id, user_id, category_id, payment_method_id, receipt_id, type, amount, currency, merchant_name, description, transaction_date, transaction_time, status, deleted_at, timestamps

## `transaction_items`
- id, transaction_id, product_name, quantity, unit_price, discount, tax, total_price, timestamps

## `receipts`
- id, user_id, file_name, file_path, file_size, mime_type, status, uploaded_at, processed_at, deleted_at, timestamps

## `receipt_ocr_results`
- id, receipt_id, raw_text, parsed_data (jsonb), confidence_score, provider, provider_request_id, error_message, timestamps

## `receipt_items`
- id, receipt_id, name, quantity, unit_price, discount, tax, total_price, confidence, timestamps

## `budgets`
- id, user_id, category_id, name, amount, period, start_date, end_date, deleted_at, timestamps

## `audit_logs`
- id, user_id, action, entity_type, entity_id, old_data, new_data, ip_address, user_agent, created_at
