<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class LeadsExport implements WithMultipleSheets, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function sheets(): array
    {
        
        set_time_limit(0);
        $sheets = [];

        $sheets[] = new LeadsSheet();
        $sheets[] = new ChallengesSheet();
        $sheets[] = new NotesSheet();
        $sheets[] = new ClaimsSheet();

        return $sheets;
    }

    // public function registerEvents(): array
    // {
    //     return [
    //         // BeforeExport::class  => function(BeforeExport $event) {
    //         //     $event->writer->setCreator('Patrick');
    //         // },
    //         AfterSheet::class    => function(AfterSheet $event) {
    //             $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //             $event->sheet->styleCells(
    //                 'A1:C1',
    //                 [
    //                     'borders' => [
    //                         'outline' => [
    //                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
    //                             'color' => ['argb' => 'FFFF0000'],
    //                         ],
    //                     ]
    //                 ]
    //             );
    //         },
    //     ];
    // }

}
