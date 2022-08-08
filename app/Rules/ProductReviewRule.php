<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductReviewRule implements Rule
{
    private $userId;

    public function __construct( int $userId)
    {
        $this->userId = $userId;
    }

    public function passes($attribute, $value)
    {
        $product = Product::where('user_id', $this->userId)
                    ->where('product_id', $value)
                    ->get()
                    ->first();

        if ($product) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The user already review this product.';
    }
}
