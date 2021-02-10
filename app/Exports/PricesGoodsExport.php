<?php

namespace App\Exports;

use App\Domain;
use App\Http\Controllers\Traits\Photable;
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

    use Exportable,
        Photable;

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
            'Название категории',
            'Описание товара',
            'Фото',
            'Популярность',
            'В наличии',
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
                    'article.photo',
                    'stocks'
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


        // TODO - 04.02.21 - Костыль с первым доменом (рх)
        $catalogId = $this->catalogId;
        $domain = Domain::whereHas('filials', function ($q) use ($catalogId) {
            $q->whereHas('catalogs_goods', function ($q) use ($catalogId) {
                $q->where('id', $catalogId);
            });
        })
            ->first();
//        dd($domain);

        $items = [];
        foreach ($pricesGoods as $priceGoods) {
//            dd($client);

            $array = [
                'id' => $priceGoods->id,
                'name' => $priceGoods->goods->article->name,
                'category_name' => $priceGoods->goods->category->name,
                'description' => $priceGoods->goods->article->description,

                'photo' => isset($priceGoods->goods->article->photo) ? 'https://' . $domain->domain . $this->getPhotoPath($priceGoods->goods->article) : '',
                'is_hit' => $priceGoods->is_hit == 1 ? 'Да' : 'Нет',
                'storage' => $priceGoods->goods->stocks->isNotEmpty() ? $priceGoods->goods->stocks->sum('free') : 'Нет',

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
