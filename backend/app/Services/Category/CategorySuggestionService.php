<?php
namespace App\Services\Category;

class CategorySuggestionService
{
    public function suggestCategory(string $merchantName): string
    {
        $merchant = strtolower(trim($merchantName));

        $rules = [
            'food' => ['kfc', 'mcd', 'restaurant', 'cafe', 'starbucks', 'warung', 'pizza'],
            'groceries' => ['indomaret', 'alfamart', 'superindo', 'carrefour', 'lotte', 'hypermart'],
            'transportation' => ['shell', 'pertamina', 'gojek', 'grab', 'bluebird', 'tol', 'parking'],
            'shopping' => ['tokopedia', 'shopee', 'lazada', 'mall', 'uniqlo', 'zara'],
            'health' => ['apotek', 'hospital', 'klinik', 'kimia farma'],
            'entertainment' => ['cinema', 'xxi', 'cgv', 'spotify', 'netflix']
        ];

        foreach ($rules as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($merchant, $keyword)) {
                    return $category;
                }
            }
        }

        return 'others';
    }
}
