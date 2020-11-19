<?php

use Illuminate\Database\Seeder;

use App\OutletsSetting;
use App\OutletsSettingsCategory;

class OutletsSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingsCategories = OutletsSettingsCategory::get();

        OutletsSetting::insert([
            [
                'name' => 'Оплата наличными',
                'alias' => 'payment-cash',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Оплата по терминалу',
                'alias' => 'payment-terminal',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Выставление банковских счетов',
                'alias' => 'bank-account',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Регистрация оплаты по расчетному счету',
                'alias' => 'payment-bank',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Оплата поинтами',
                'alias' => 'payment-point',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Оплата сертификатами',
                'alias' => 'payment-certificate',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],
            [
                'name' => 'Оплата под З/П',
                'alias' => 'payment-salary',
                'category_id' => $settingsCategories->firstWhere('alias', 'payments-types')->id
            ],

            [
                'name' => 'Использовать кассы',
                'alias' => 'use-cash-register',
                'category_id' => $settingsCategories->firstWhere('alias', 'cash-register')->id
            ],
            [
                'name' => 'Вносить изменения в проведенные платежи',
                'alias' => 'payment-edit',
                'category_id' => $settingsCategories->firstWhere('alias', 'cash-register')->id
            ],
            [
                'name' => 'Выбор даты платежа',
                'alias' => 'payment-date-change',
                'category_id' => $settingsCategories->firstWhere('alias', 'others')->id
            ],


            [
                'name' => 'Списание с убытком',
                'alias' => 'dismiss-with-loss',
                'category_id' => $settingsCategories->firstWhere('alias', 'dismiss')->id
            ],
            [
                'name' => 'Списание без убытка',
                'alias' => 'dismiss-without-loss',
                'category_id' => $settingsCategories->firstWhere('alias', 'dismiss')->id
            ],

            [
                'name' => 'Отображать номиналы',
                'alias' => 'denominations-show',
                'category_id' => $settingsCategories->firstWhere('alias', 'others')->id
            ],
            [
                'name' => 'Автозаполнение суммы',
                'alias' => 'amount-autofill',
                'category_id' => $settingsCategories->firstWhere('alias', 'others')->id
            ],
            [
                'name' => 'Отображение отмененных платежей',
                'alias' => 'canceled-payments-show',
                'category_id' => $settingsCategories->firstWhere('alias', 'others')->id
            ],
        ]);
    }
}
