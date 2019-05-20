<?php
namespace App\Exports;

use App\Note;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotesSheet implements FromCollection, WithTitle, WithHeadings
{

    /**
     * @return Builder
     */
    public function collection()
    {
        $items = Note::with([
            'author',
        ])
        ->select([
            'id',
            'created_at',
            'body',
            'author_id',
            'notes_id',
            'notes_type',
        ])
        ->where('notes_type', 'App\Lead')
        ->get()
        ;
        // dd($items->first());


        $notes = [];
        foreach ($items as $item) {
            $note = [
                'id' => $item->id,
                'created_at' => $item->created_at,
                'body' => $item->body,
                'author' => $item->author->name,
                'lead_id' => $item->notes_id,
            ];
            $notes[] = collect($note);

            // collect($challenge);
        }
        $notes = collect($notes);
        // dd($challenges[0]);

        return $notes;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Комментарии';
    }

    public function headings(): array
    {
        return [
            'Id коммента',
            'Дата',
            'Описание',
            'Автор',
            'Id лида',
        ];
    }
}