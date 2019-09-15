<?php

namespace App\Http\Controllers\Traits;

use App\Estimate;
use App\PricesGoods;
use App\EstimatesItem;

trait EstimateControllerTrait
{
	public function createEstimateFromCart($cart, $lead)
    {

        // Находим или создаем заказ для лида
        $estimate = Estimate::firstOrCreate([
            'lead_id' => $lead->id,
            'company_id' => $lead->company->id,
        ], [
            'author_id' => 1,
            'number' => $lead->case_number,
            'date' => $lead->created_at,
        ]);

        $prices_goods_ids = array_keys($cart['prices']);
        $prices_goods = PricesGoods::with('goods')
            ->find($prices_goods_ids);

        $data = [];
        foreach ($prices_goods as $price_goods) {
            $data[] = new EstimatesItem([
                'product_id' => $price_goods->goods->id,
                'product_type' => 'App\Goods',

                'price_product_id' => $price_goods->id,
                'price_product_type' => 'App\PricesGoods',

                'company_id' => $lead->company->id,
                'author_id' => 1,

                'price' => $price_goods->price,
                'count' => $cart['prices'][$price_goods->id]['count'],

                'sum' => $cart['prices'][$price_goods->id]['count'] * $price_goods->price
            ]);
        }

        $estimate->items()->saveMany($data);

        return $estimate;
    }
}