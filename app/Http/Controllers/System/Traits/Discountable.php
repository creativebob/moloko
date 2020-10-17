<?php

namespace App\Http\Controllers\System\Traits;

use App\Client;
use App\Models\System\Documents\Estimate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

trait Discountable
{
    public function getDynamicDiscounts($discount, $totalWithoutDiscounts)
    {
        $break = false;
        $total = 0;
        switch ($discount->mode) {
            case(1):
                $discountInCurrency = $totalWithoutDiscounts / 100 * $discount->percent;
                $total = $totalWithoutDiscounts - $discountInCurrency;
                break;
            case(2):
                $total = $totalWithoutDiscounts - $discount->currency;
                break;
        }
        
        if ($discount->is_block == 1) {
            $break = true;
        }
        
        $res = [
            'total' => $total,
            'break' => $break
        ];
        
        return $res;
    }
}
