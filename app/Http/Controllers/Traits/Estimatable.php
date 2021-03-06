<?php

namespace App\Http\Controllers\Traits;

use App\Models\System\Documents\EstimatesGoodsItem;
use Illuminate\Http\Request;

trait Estimatable
{

    /**
     * Аггрегация сметы
     *
     * @param $estimate
     */
    public function aggregateEstimate($estimate)
    {
        $estimate->load([
            'goods_items.price_goods',
            'services_items.price_service',
        ]);

        $cost = 0;
        $amount = 0;
        $points = 0;

        $priceDiscount = 0;
        $catalogsItemDiscount = 0;
        $estimateDiscount = 0;
        $clientDiscount = 0;
        $manualDiscount = 0;

        $total = 0;
        $totalPoints = 0;
        $totalBonuses = 0;

        $shareCurrency = 0;
        $principalCurrency = 0;

        $catalogsGoodsIds = [];
        $catalogsServicesIds = [];

        if ($estimate->goods_items->isNotEmpty()) {
            $cost += $estimate->goods_items->sum('cost');
            $amount += $estimate->goods_items->sum('amount');
            $points += $estimate->goods_items->sum('points');

            $priceDiscount += $estimate->goods_items->sum('price_discount');
            $catalogsItemDiscount += $estimate->goods_items->sum('catalogs_item_discount');
            $estimateDiscount += $estimate->goods_items->sum('estimate_discount');
            $clientDiscount += $estimate->goods_items->sum('client_discount_currency');
            $manualDiscount += $estimate->goods_items->sum('manual_discount_currency');

            $total += $estimate->goods_items->sum('total');
            $totalPoints += $estimate->goods_items->sum('total_points');
            $totalBonuses += $estimate->goods_items->sum('total_bonuses');

            $shareCurrency += $estimate->goods_items->sum('share_currency');
            $principalCurrency += $estimate->goods_items->sum('principal_currency');

            $groupedGoodsItems = $estimate->goods_items->groupBy('price_goods.catalogs_goods_id');
            $catalogsGoodsIds = $groupedGoodsItems->keys();
        }

        if ($estimate->services_items->isNotEmpty()) {
            $cost += $estimate->services_items->sum('cost');
            $amount += $estimate->services_items->sum('amount');
            $points += $estimate->services_items->sum('points');

            $priceDiscount += $estimate->services_items->sum('price_discount');
            $catalogsItemDiscount += $estimate->services_items->sum('catalogs_item_discount');
            $estimateDiscount += $estimate->services_items->sum('estimate_discount');
            $clientDiscount += $estimate->services_items->sum('client_discount_currency');
            $manualDiscount += $estimate->services_items->sum('manual_discount_currency');

            $total += $estimate->services_items->sum('total');
            $totalPoints += $estimate->services_items->sum('total_points');
            $totalBonuses += $estimate->services_items->sum('total_bonuses');

            $shareCurrency += $estimate->services_items->sum('share_currency');
            $principalCurrency += $estimate->services_items->sum('principal_currency');

            $groupedServicesItems = $estimate->services_items->groupBy('price_service.catalogs_service_id');
            $catalogsServicesIds = $groupedServicesItems->keys();
        }

        // Скидки
        $discountCurrency = 0;
        $discountPercent = 0;
        if ($total > 0) {
            $discountCurrency = $amount - $total;
            $discountPercent = $discountCurrency * 100 / $amount;
        }

        // Маржа
        $marginCurrency = $total - $cost;
        if ($total > 0) {
            $marginPercent = ($marginCurrency / $total * 100);
        } else {
            $marginPercent = $marginCurrency * 100;
        }

        $data = [
            'cost' => $cost,
            'amount' => $amount,
            'points' => $points,

            'price_discount' => $priceDiscount,
            'catalogs_item_discount' => $catalogsItemDiscount,
            'estimate_discount' => $estimateDiscount,
            'client_discount' => $clientDiscount,
            'manual_discount' => $manualDiscount,

            'discount_currency' => $discountCurrency,
            'discount_percent' => $discountPercent,

            'total' => $total,
            'total_points' => $totalPoints,
            'total_bonuses' => $totalBonuses,

            'share_currency' => $shareCurrency,
            'principal_currency' => $principalCurrency,

            'margin_currency' => $marginCurrency,
            'margin_percent' => $marginPercent,
        ];

        $estimate->update($data);

        $estimate->catalogs_goods()->sync($catalogsGoodsIds);
        $estimate->catalogs_services()->sync($catalogsServicesIds);
    }
}
