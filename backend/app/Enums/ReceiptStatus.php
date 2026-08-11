<?php
namespace App\Enums;

enum ReceiptStatus: string
{
    case UPLOADED = 'uploaded';
    case PROCESSING = 'processing';
    case OCR_COMPLETED = 'ocr_completed';
    case PARSING = 'parsing';
    case READY_FOR_REVIEW = 'ready_for_review';
    case CONFIRMED = 'confirmed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
