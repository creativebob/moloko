<?php

namespace App\Exports;

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

    use Exportable;

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
            'Описание услуги',
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

        $items = [];
        foreach ($pricesServices as $pricesService) {
//            dd($client);

            $array = [
                'id' => $pricesService->id,
                'name' => $pricesService->service->process->name,
                'description' => $pricesService->service->process->description,
                'category_name' => $pricesService->service->category->name,
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
