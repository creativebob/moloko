<?php

use Illuminate\Database\Seeder;
use App\UnitsCategory;

class CurrencyTableSeeder extends Seeder
{

    public function run()
    {
    	DB::table('units_categories')->insert([
    		[
    			'name' => 'Валюта',
    			'unit' => 'рубль',
    			'abbreviation' => 'руб',
    			'system_item' => null,
    			'author_id' => 1,
    			'moderation' => null,
    		],
    	]);

        $units_category = UnitsCategory::where('unit', 'рубль')->first();

        DB::table('units')->insert([
            [
                'name' => 'Рубль',
                'abbreviation' => 'руб',
                'ratio' => 1,
                'units_category_id' => $units_category->id,
                'system_item' => 1,
                'author_id' => 1,
                'moderation' => null,
            ],
        ]);
    }
}
