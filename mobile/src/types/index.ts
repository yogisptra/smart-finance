export interface Transaction {
  id: number;
  type: 'income' | 'expense';
  amount: number;
  currency: string;
  merchant_name: string;
  transaction_date: string;
  category: { id: number; name: string };
  paymentMethod: { id: number; name: string };
}

export interface DashboardData {
  balance: number;
  income: number;
  expense: number;
  recent_transactions: Transaction[];
}

export interface Category {
  id: number;
  name: string;
  type: string;
}

export interface PaymentMethod {
  id: number;
  name: string;
  type: string;
}

export interface Receipt {
  id: number;
  status: 'uploaded' | 'processing' | 'ocr_completed' | 'parsing' | 'ready_for_review' | 'confirmed' | 'failed';
  file_name: string;
  ocrResult?: {
    parsed_data: ParsedReceipt;
  };
  items?: ReceiptItem[];
}

export interface ParsedReceipt {
  merchantName?: string;
  transactionDate?: string;
  transactionTime?: string;
  subtotal?: number;
  tax?: number;
  total?: number;
}

export interface ReceiptItem {
  id: number;
  name: string;
  quantity: number;
  unit_price: number;
  total_price: number;
}
