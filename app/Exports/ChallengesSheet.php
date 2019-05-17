<?php
namespace App\Exports;

use App\Challenge;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChallengesSheet implements FromCollection, WithTitle, WithHeadings
{

    /**
     * @return Builder
     */
    public function collection()
    {
        $items = Challenge::with([
            'author',
            'appointed',
            'challenge_type'
        ])
        ->select([
            'id',
            'created_at',
            'description',
            'appointed_id',
            'deadline_date',
            'challenges_type_id',
            'subject_id',
            'author_id',
            'subject_type',
        ])
        ->whereNull('finisher_id')
        ->where('subject_type', 'App\Lead')
        // ->pluck(
        //     'created_at',
        //     'id',
        //     'description',
        //     'appointed.name',
        //     'deadline_date',
        //     'challenges_type.name',
        //     'subject_id',
        //     'author.name'
        // )
        ->get()
        ;


        $challenges = [];
        foreach ($items as $item) {
            $challenge = [
                'id' => $item->id,
                'created_at' => $item->created_at,
                'description' => $item->description,
                'appointed' => $item->appointed->name,
                'deadline_date' => $item->deadline_date,
                'challenge_type' => $item->challenge_type->name ?? '',
                'author' => $item->author->name,
                'lead_id' => $item->subject_id,
            ];
            $challenges[] = collect($challenge);

            // collect($challenge);
        }
        $challenges = collect($challenges);
        // dd($challenges[0]);

        return $challenges;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Задачи';
    }

    public function headings(): array
    {
        return [
            'Id задачи',
            'Дата',
            'Описание',
            'Исполнитель',
            'Дедлайн',
            'Тип',
            'Автор',
            'Id лида',
        ];
    }
}
