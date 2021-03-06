<?php

namespace App\Observers\System\Documents\Traits;

trait EstimateItemable
{
    /**
     * Расчет итоговых значений пункта сметы
     *
     * @param $item
     */
    public function setAggregations($item)
    {
        $saleMode = $item->sale_mode;

        switch ($saleMode) {
            case (1):
                $item->total_points = 0;
                $item->total_bonuses = 0;

                // Основные расчеты
                $item->cost = $item->cost_unit * $item->count;
                $item->amount = $item->price * $item->count;

                // Скидки
                // Если есть ручная скидка
                if ($item->is_manual == 1) {

                    $item->price_discount = 0;
                    $item->total_price_discount = $item->amount;

                    $item->catalogs_item_discount = 0;
                    $item->total_catalogs_item_discount = $item->amount;

                    $item->estimate_discount = 0;
                    $item->total_estimate_discount = $item->amount;

                    $item->client_discount_currency = 0;
                    $item->total_client_discount = $item->amount;

                    $item->total_manual_discount = $item->amount - ($item->manual_discount_currency * $item->count);
                    $item->total = $item->total_manual_discount;

                    $item->total_computed_discount = 0;

                    $item->discount_currency = $item->manual_discount_currency * $item->count;
                    $item->discount_percent = $item->manual_discount_percent;
                } else {
                    // Иначе рассчитываем
                    $item->price_discount = $item->price_discount_unit * $item->count;
                    $item->total_price_discount = $item->amount - $item->price_discount;

                    $item->catalogs_item_discount = $item->catalogs_item_discount_unit * $item->count;
                    $item->total_catalogs_item_discount = $item->total_price_discount - $item->catalogs_item_discount;

                    $item->estimate_discount = $item->estimate_discount_unit * $item->count;
                    $item->total_estimate_discount = $item->total_catalogs_item_discount - $item->estimate_discount;

                    if ($item->client_discount_percent > 0) {
                        $item->client_discount_unit_currency = $item->total_estimate_discount / 100 * $item->client_discount_percent / $item->count;
                        $item->client_discount_currency = $item->client_discount_unit_currency * $item->count;
                    } else {
                        $item->client_discount_unit_currency = 0;
                        $item->client_discount_currency = 0;
                    }
                    $item->total_client_discount = $item->total_estimate_discount - $item->client_discount_currency;

                    $item->total = $item->total_client_discount;

                    $item->discount_currency = $item->amount - $item->total;
                    if ($item->discount_currency > 0) {
                        $item->discount_percent = $item->discount_currency * 100 / $item->amount;
                    } else {
                        $item->discount_percent = 0;
                    }

                    $item->computed_discount_percent = $item->discount_percent;
                    $item->computed_discount_currency = $item->discount_currency / $item->count;
                    $item->total_computed_discount = $item->discount_currency;

                    $item->manual_discount_currency = 0;
                    $item->manual_discount_percent = 0;
                    $item->total_manual_discount = 0;
                }

                // Агент
                if ($item->share_percent > 0) {
                    $item->share_currency = $item->total / 100 * $item->share_percent;
                    $item->principal_currency = $item->total - $item->share_currency;

                    // Маржа
                    $item->margin_currency_unit = $item->principal_currency / $item->count;
                    $item->margin_currency = $item->principal_currency;

                    if ($item->total > 0) {
                        $item->margin_percent_unit = 100;
                        $item->margin_percent = 100;
                    } else {
                        $item->margin_percent_unit = 100;
                        $item->margin_percent = 100;
                    }
                } else {
                    $item->share_currency = 0;
                    $item->principal_currency = 0;

                    // Маржа
                    if ($item->is_manual == 0) {
                        $totalPrice = $item->price - $item->price_discount_unit - $item->catalogs_item_discount_unit - $item->estimate_discount_unit - $item->client_discount_unit_currency;
                    } else {
                        $totalPrice = $item->price - $item->manual_discount_currency;
                    }
                    $item->margin_currency_unit = $totalPrice - $item->cost_unit;
                    $item->margin_currency = $item->total - $item->cost;

                    if ($item->total > 0) {
                        $item->margin_percent_unit = ($item->margin_currency_unit / $totalPrice * 100);
                        $item->margin_percent = ($item->margin_currency / $item->total * 100);
                    } else {
                        $item->margin_percent_unit = ($item->margin_currency_unit * 100);
                        $item->margin_percent = ($item->margin_currency * 100);
                    }
                }

                break;

            case (2):
                $item->amount = 0;

                $item->price_discount = 0;
                $item->total_price_discount = 0;

                $item->catalogs_item_discount = 0;
                $item->total_catalogs_item_discount = 0;

                $item->estimate_discount = 0;
                $item->total_estimate_discount = 0;

                $item->manual_discount_currency = 0;
                $item->manual_discount_percent = 0;
                $item->total_manual_discount = 0;

                $item->client_discount_currency = 0;
                $item->total_client_discount = 0;

                $item->computed_discount_percent = 0;
                $item->computed_discount_currency = 0;
                $item->total_computed_discount = 0;

                $item->total = 0;
                $item->total_bonuses = 0;
                $item->total_points = $item->count * $item->points;

                $item->discount_currency = 0;
                $item->discount_percent = 0;

                $item->margin_currency = 0;
                $item->margin_percent = 0;
                break;
        }
    }
}
