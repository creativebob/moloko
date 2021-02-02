<?php

namespace App\Exports;

use App\PricesGoods;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PricesGoodsExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    protected $catalogId;

    /**
     * PricesGoodsExport constructor.
     * @param $catalogId
     */
    public function __construct($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    use Exportable;

    /**
     * Название листа
     *
     * @return string
     */
    public function title(): string
    {
        return 'Товары';
    }

    /**
     * Заголовки столцов
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Id',
            'Название товара',
            'Описание товара',
            'Имя категории',
            'Цена',
            'Цена в РХ',
        ];
    }

    /**
     * Данные в коллекции
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function collection()
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('prices_goods', true, getmethod('index'));

        $pricesGoods = PricesGoods::with([
            'goods' => function ($q) {
                $q->with([
                    'category',
                    'article'
                ]);
            },
        ])
            ->whereHas('goods', function($q){
                $q->whereHas('article', function ($q) {
                    $q->where('draft', false);
                })
                    ->where('archive', false);
            })
            ->where('catalogs_goods_id', $this->catalogId)
            ->where('archive', false)
            ->companiesLimit($answer)
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->filter()

            ->oldest('id')
            ->get();

        $items = [];
        foreach ($pricesGoods as $priceGoods) {
//            dd($client);

            $array = [
                'id' => $priceGoods->id,
                'name' => $priceGoods->goods->article->name,
                'description' => $priceGoods->goods->article->description,
                'category_name' => $priceGoods->goods->category->name,
                'price' => $priceGoods->price,
                'points' => $priceGoods->points,
            ];
            $items[] = collect($array);
        }
        $items = collect($items);
//        dd($items->first());

        return $items;
    }
}
