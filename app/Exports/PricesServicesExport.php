<?php

namespace App\Exports;

use App\Domain;
use App\Http\Controllers\Traits\Photable;
use App\PricesService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PricesServicesExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    protected $catalogId;

    /**
     * PricesServicesExport constructor.
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
        return 'Услуги';
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
            'Название услуги',
            'Название категории',
            'Описание услуги',
            'Фото',
            'Популярность',
//            'В наличии',
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

        $pricesServices = PricesService::with([
            'service' => function ($q) {
                $q->with([
                    'category',
                    'process'
                ]);
            },
        ])
            ->whereHas('service', function($q){
                $q->whereHas('process', function ($q) {
                    $q->where('draft', false);
                })
                    ->where('archive', false);
            })
            ->where('catalogs_service_id', $this->catalogId)
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
            $q->whereHas('catalogs_services', function ($q) use ($catalogId) {
                $q->where('id', $catalogId);
            });
        })
            ->first();
//        dd($domain);

        $items = [];
        foreach ($pricesServices as $pricesService) {
//            dd($client);

            $array = [
                'id' => $pricesService->id,
                'name' => $pricesService->service->process->name,
                'category_name' => $pricesService->service->category->name,
                'description' => $pricesService->service->process->description,

                'photo' => isset($pricesService->service->process->photo) ? 'https://' . $domain->domain . $this->getPhotoPath($pricesService->service->process) : '',
                'is_hit' => $pricesService->is_hit == 1 ? 'Да' : 'Нет',
//                'storage' => $pricesService->goods->stocks->isNotEmpty() ? $pricesService->goods->stocks->sum('free') : 'Нет',

                'price' => $pricesService->price,
                'points' => $pricesService->points,
            ];
            $items[] = collect($array);
        }
        $items = collect($items);
//        dd($items->first());

        return $items;
    }
}
