<?php
namespace App\Exports;

use App\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsSheet implements FromCollection, WithTitle, WithHeadings
{

    /**
     * @return Builder
     */
    public function collection()
    {
        $items = Lead::
        with([
//        	'manager',
//            'location',
//            'source',
//            'lead_type',
//            'lead_method',
//            'choice',
//            'manager',
//            'stage',
            'main_phones',
//            'author',
//            'phones'
        ])
        ->select([
            'id',
//            'created_at',
//            'case_number',
//            'name',
//            'company_name',
//            'location_id',
//            'email',
//            'badget',
//            'stage_id',
//            'manager_id',
//            'lead_type_id',
//            'lead_method_id',
//            'choice_id',
//            'choice_type',
//            'source_id',
//            'utm_term',
//            'author_id',
            'draft',
        ])
        ->whereNull('draft')
        ->get()
        ;


        $leads = [];
        foreach ($items as $item) {

        	// dd($item);
            $lead = [
//                'id' => $item->id,
//                'created_at' => $item->created_at,
//                'case_number' => $item->case_number,
//                'name' => $item->name,
//                'company_name' => $item->company_name,
//                'city' => $item->location->city->name ?? '',
//                'address' => $item->location->address ?? '',
                    'phone' => isset($item->main_phone->phone) ? $item->main_phone->phone : 'Номер не указан',
//                'phone' => isset($item->main_phone->phone) ? decorPhone($item->main_phone->phone) : 'Номер не указан',
//                'email' => $item->email,
//                'badget' => $item->badget,
//                'stage' => $item->stage->name,
//                'manager' => $item->manager->name ?? '',
//                'lead_type' => $item->lead_type->name ?? '',
//                'lead_method' => $item->lead_method->name ?? '',
//                'choice' => $item->choice->name ?? '',
//                'source' => $item->source->name ?? '',
//                'utm_term' => $item->utm_term,
//                'author' => $item->author->name,
            ];
            $leads[] = collect($lead);

            // collect($challenge);
        }
        $leads = collect($leads);
        // dd($challenges[0]);

        return $leads;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Лиды';
    }

    public function headings(): array
    {
        return [
//            'Id лида',
//            'Дата',
//            'Номер',
//            'Контакт',
//            'Компания (если есть)',
//            'Город',
//            'Адрес',
            'Телефон',
//            'Почта',
//            'Сумма сделки',
//            'Этап',
//            'Менеджер',
//            'Тип обращения',
//            'Способ обращения',
//            'Интерес',
//            'Источник',
//            'Ключевая фраза',
//            'Автор',
        ];
    }
}
