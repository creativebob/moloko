<?php
namespace App\Exports\Sheets;

use App\Article;
use App\Models\System\RollHouse\AuthCustomuser;
use App\Models\System\RollHouse\Check;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RollHouseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{

    /**
     * @return Builder
     */
    public function collection()
    {

        $groupedChecks = Check::with([
            'employer.user.user'
        ])
            ->where('client_id', 30640)
            ->get()
            ->groupBy('admin_id');
//        dd($groupedChecks);

        $checks = [];
        foreach($groupedChecks as $adminId => $groupedCheck) {
            $admin = AuthCustomuser::with('user')
                ->where('user_ptr_id', $adminId)
            ->first();
//            dd($groupedCheck);

            foreach($groupedCheck as $curCheck) {
                switch ($curCheck->progress) {
                    case 1:
                        $status = 'Открыт';
                        break;

                    case 2:
                        $status = 'Закрыт';
                        break;

                    case 3:
                        $status = 'Списан';
                        break;
                }

                if (isset($curCheck->table)) {
                    if ($curCheck->table == 99) {
                        $table = 'Самовывоз';
                    } else {
                        $table = $curCheck->table;
                    }
                } else {
                    $table = null;
                }

                $check = [
                    'adminId' => $admin->user->id,
                    'admin' => $admin->user->first_name,
                    'checkId' => $curCheck->id,
                    'date' => $curCheck->created->format('d.m.Y'),
                    'time' => $curCheck->created->format('H:i:s'),
                    'summa' => $curCheck->summa,
                    'table' => $table,
                    'status' => $status,
                    'employeer' => isset($curCheck->employer_id) ? $curCheck->employer->user->user->first_name . " ({$curCheck->employer->user->user->id})" : null,
                ];
                $checks[] = collect($check);
            }
        }
        $checks = collect($checks);

        return $checks;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Заказы';
    }

    public function headings(): array
    {
        return [
            'Id админа',
            'Админ',
            'Id заказа',
            'Дата',
            'Время',
            'Стоимость',
            'Стол',
            'Статус',
            'На кого списано',
        ];
    }
}
