# OCR & AI Parsing Architecture

## 1. Flow
1. User uploads a receipt image.
2. `ReceiptController` stores the file locally and dispatches `ProcessReceiptJob` (which triggers the OCR flow).
3. `PerformOCRJob` reads the image and calls `OCRServiceInterface`.
4. Extracted raw text is saved to `ReceiptOcrResult` (status: `ocr_completed`).
5. `ParseReceiptJob` is dispatched, taking raw text and sending it to `ReceiptParserInterface`.
6. AI responds with structured JSON that strictly matches `ParsedReceipt` DTO.
7. Data is normalized and saved to `ReceiptOcrResult` and `ReceiptItems` (status: `ready_for_review`).

## 2. Abstraction
- The provider is strictly hidden behind interfaces (`OCRServiceInterface` and `ReceiptParserInterface`).
- The current implementation is a `DummyOCRService` and `DummyReceiptParser`.
- To swap with a real provider, implement the interface and bind it in `AppServiceProvider`.

## 3. Cost & Retry Control
- Jobs use exponential backoff (`$tries = 3`, `$backoff = [10, 30, 60]`).
- Future scaling requires `receipt_processing_logs` table for detailed metric tracking.
