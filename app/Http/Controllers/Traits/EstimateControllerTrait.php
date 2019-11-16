<?php

namespace App\Http\Controllers\Traits;

use App\Models\Project\Estimate;
use App\PricesGoods;
use App\Models\Project\EstimatesGoodsItem;
use App\Stock;

trait EstimateControllerTrait
{
	public function createEstimateFromCart($cart, $lead)
    {

        // TODO - 15.11.19 - Склад должен браться из настроек, пока берем первый по филиалу
        $stock_id = Stock::where('filial_id', $lead->filial_id)->value('id');

        // Находим или создаем заказ для лида
        $estimate = Estimate::create([
            'lead_id' => $lead->id,
            'filial_id' => $lead->filial_id,
            'company_id' => $lead->company->id,
            'stock_id' => $stock_id,
            'date' => now()->format('Y-m-d'),
            'number' => $lead->case_number,
            'author_id' => $lead->author_id,

        ]);

        $prices_goods_ids = array_keys($cart['prices']);
        $prices_goods = PricesGoods::with('goods')
            ->find($prices_goods_ids);

        $data = [];
        foreach ($prices_goods as $price_goods) {
            $data[] = new EstimatesGoodsItem([
                'goods_id' => $price_goods->goods->id,

                'price_id' => $price_goods->id,
                'stock_id' => $estimate->stock_id,

                'company_id' => $lead->company->id,
                'author_id' => 1,

                'price' => $price_goods->price,
                'count' => $cart['prices'][$price_goods->id]['count'],

                'amount' => $cart['prices'][$price_goods->id]['count'] * $price_goods->price
            ]);
        }

        $estimate->goods_items()->saveMany($data);

        return $estimate;
    }
}