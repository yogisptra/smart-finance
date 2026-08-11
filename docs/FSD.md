# Functional Specification Document (FSD)
## Smart Finance App

### 1. Product Overview
Smart Finance is a personal finance mobile application that helps users track their income, expenses, and budgets. Its core feature is an AI-powered Receipt Scanner that automatically extracts transaction data from uploaded receipts.

### 2. Core Features
- **User Authentication**: Register, login, profile management.
- **Dashboard**: Overview of current balance, income vs. expense, expense breakdown by category, budget progress, and recent transactions.
- **Transaction Management**: Manual entry of transactions (income/expense), assignment of categories, and payment methods.
- **Receipt Processing**: Users can scan/upload a receipt. The app extracts text using OCR and structures it into a JSON format using an AI Parser. Users review the output before confirming the transaction.
- **Budgeting**: Users can set budgets for specific categories over various periods (daily, weekly, monthly, yearly, custom).
- **Reporting**: Aggregate reports by category, trend over time, and summary.

### 3. User Experience Principles
- Mobile-first, fast, and readable.
- Minimizing manual data entry through OCR.
- Clear Empty States, Loading States, and Error UX.
- Smart Insights (future scaling).

### 4. Non-Functional Requirements
- Support offline resilience (basic connection handling).
- Scalable PostgreSQL structure.
- Asynchronous jobs for heavy operations (OCR/AI).
