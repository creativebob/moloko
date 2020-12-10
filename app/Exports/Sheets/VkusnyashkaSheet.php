<?php
namespace App\Exports\Sheets;

use App\Article;
use App\Lead;
use App\Models\System\RollHouse\AuthCustomuser;
use App\Models\System\RollHouse\Check;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VkusnyashkaSheet implements FromCollection, ShouldAutoSize
{

    use Exportable;

    /**
     * @return Builder
     */
    public function collection()
    {

        $groupedLeads = Lead::with([
            'main_phones'
        ])
            ->whereDate('created_at', '>=', '2020-09-01')
            ->whereDate('created_at', '<=', '2020-12-31')
            ->whereIn('stage_id', [15, 2, 16, 6, 10, 12])
            ->where('draft', false)
            ->get()
            ->groupBy('main_phone.phone')
        ;
//        dd($groupedLeads);

        $phones = [];
        foreach($groupedLeads as $phone => $leads) {
            $phones[] = collect($phone);
//            dd($phones);
        }

        return collect($phones);
    }
}
