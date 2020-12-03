<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Storage;

use App\Goods;
use App\PricesGoods;
use App\CatalogsGoods;

use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Функция для тестов
     */
    public function test()
    {
        dd(__METHOD__);
    }

        /**
     * XML тест для выгрузки на Yandex Маркет
     */
    public function xml_test()
    {

        $user = Auth::user();

        if(empty($user->company)){echo "Пользователь не авторизован";}

        $company = $user->company;
        $domain = $company->sites->first()->domains->first()->domain;


        // Формируем список категорий (В нашем случае - пунктов каталога)
        $catalogs_goods = CatalogsGoods::with('items')->where('is_exported_yml', true)->get();

        foreach ($catalogs_goods as $catalog){
            foreach ($catalog->items as $item){

                if($item->parent_id){
                    $array_categories[] = [
                        'category' => $item->name, '_attributes' => [
                            'id' => $item->id,
                            'parent_id' => $item->parent_id
                        ]
                    ];                    
                } else {
                    $array_categories[] = [
                        'category' => $item->name, '_attributes' => [
                            'id' => $item->id,
                        ]
                    ]; 
                }


            }

            // Формируем список offers (Товаров)
            $prices_goods = PricesGoods::with([
                'goods' => function ($q) {
                    $q->with([
                        'article' => function ($q) {
                            $q->with([
                               'unit',
                               'group.unit'
                            ]);
                        }
                    ]);
                },
                'catalog',
                'catalogs_item',
                'currency',
                'discount_price',
                'discount_catalogs_item'
            ])
                ->where('company_id', $catalog->company_id)
                ->where('is_exported_to_market', true)
                ->whereHas('goods', function($q){
                    $q->whereHas('article', function ($q) {
                        $q->where('draft', false);
                    })
                        ->where('archive', false);
                })

                ->where([
                    'archive' => false,
                    'catalogs_goods_id' => $catalog->id,
                ])
                ->orderBy('sort')
                ->get();

            }


            foreach ($prices_goods as $price){

                $path = getPhotoPathPlugEntity($price->goods, 'medium');

                $array_offers[] = ['offer' => [
                        'name' => $price->goods->article->name, 
                        'vendor' => $company->legal_form->name . ' ' . $price->goods->article->manufacturer->company->name,
                        // 'vendorCode' => $price->goods->article->manufacturer->code,
                        'url' => 'https://' . $domain . '/prices-goods/' . $price->id,
                        'price' => $price->price,
                        // 'oldprice' => $price->oldprice,
                        // 'purchase_price' => $price->goods->article->cost_default,
                        'enable_auto_discounts' => 'false',
                        'currencyId' => $price->currency->short,
                        'categoryId' => $price->catalog->id,
                        'category' => $price->catalog->name,
                        'picture' =>'https://' . $company->sites->first()->domains->first()->domain . '/' . $path,
                        'delivery' => 'true',
                        'pickup' => 'false',

                        'param' => [
                            '_attributes' => [
                                'name' => 'Вес',
                                'unit' => 'кг'
                            ],
                            '_value' => num_format($price->goods->article->weight, 2)
                        ],
                        'store' => $price->is_preorder,
                        'description' => $price->goods->article->description,

                        '_attributes' => [
                            'id' => $price->id, 
                            'bid' => 80
                        ]

                    ]
                ];
            }
       
            // dd($prices_goods);

        $root = [
            'rootElementName' => 'yml_catalog',
            '_attributes' => [
                'date' => '2020-12-31 20:05',
            ],
        ];


        $yml = [
            'shop' => [
                'name' => $company->designation,
                'company' => $company->legal_form->name . ' ' . $company->name,
                'url' => $domain,
                'platform' => 'CreativeBob ERPSystem',
                'version' => '1.0',
                'agency' => 'Студия мелиа проектов CreativeBob',
                'email' => 'smpcreativebob@gmail.com',
                'currencies' => [
                    'currency' => [
                        '_attributes' => [
                            'id' => 'RUB',
                            'rate' => 1
                        ]
                    ]
                ],
                'categories' => $array_categories,
                'offers' => $array_offers
            ]
        ];

        $arrayToXml = new ArrayToXml($yml, $root, [], 'UTF-8', '1.1');
        $arrayToXml->setDomProperties(['formatOutput' => true]);
        $result = $arrayToXml->prettify()->toXml();

        // dd($result);

        Storage::disk('local')->put('yml_catalog.xml', $result);
        dd('Сохранено');


    }
}
