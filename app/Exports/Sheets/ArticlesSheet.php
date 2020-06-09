<?php
namespace App\Exports\Sheets;

use App\Article;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticlesSheet implements FromCollection, WithTitle, WithHeadings
{

    /**
     * @return Builder
     */
    public function collection()
    {
        $items = Article::
        with([
            'cur_goods.category',
        ])
            ->has('cur_goods')
        ->get([
            'id',
            'name',
            'cost_default'
        ]);


        $articles = [];
        foreach ($items as $item) {

//        	 dd($item);
            $article = [
                'id' => $item->id,
                'category' => $item->cur_goods->category->name,
                'name' => $item->name,
//                'cost_default' => $item->id,
            ];
            $articles[] = collect($article);

            // collect($challenge);
        }
        $articles = collect($articles);

        return $articles;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Товары';
    }

    public function headings(): array
    {
        return [
            'Id',
            'Категория',
            'Название',
            'Себестоимость',
        ];
    }
}
