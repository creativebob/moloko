<?php
namespace App\Exports;

use App\Claim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClaimsSheet implements FromCollection, WithTitle, WithHeadings
{

    /**
     * @return Builder
     */
    public function collection()
    {
        $items = Claim::with([
            'author',
            'source_lead',
            'lead',
            'manager',
        ])
        ->select([
            'id',
            'created_at',
            'body',
            'case_number',
            'source_lead_id',
            'lead_id',
            'manager_id',
            'status',
            'author_id',
        ])
        ->get()
        ;
        // dd($items);


        $claims = [];
        foreach ($items as $item) {
            $claim = [
                'id' => $item->id,
                'created_at' => $item->created_at,
                'body' => $item->body,
                'case_number' => $item->case_number,
                'source_lead' => $item->source_lead->name ?? '',
                'status' => ($item->status == 1) ? 'Не выполнена' : 'Выполнена',
                'author' => $item->author->name,
                'lead_id' => $item->lead_id,
            ];
            $claims[] = collect($claim);

            // collect($challenge);
        }
        $claims = collect($claims);
        // dd($challenges[0]);

        return $claims;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Рекламации';
    }

    public function headings(): array
    {
        return [
            'Id рекламации',
            'Дата',
            'Описание',
            'Номер',
            'Лид источник',
            'Статус',
            'Автор',
            'Id лида',
        ];
    }
}