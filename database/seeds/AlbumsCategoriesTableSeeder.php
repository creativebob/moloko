<?php

use Illuminate\Database\Seeder;

class AlbumsCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('albums_categories')->insert([
        	[
                'company_id' => 1,
		        'albums_category_name' => 'Фотоальбом',
		        'albums_category_parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
        	],
        	[
                'company_id' => 1,
		        'albums_category_name' => 'Личный',
		        'albums_category_parent_id' => 1,
                'category_status' => null,
                'author_id' => 1,
        	],
            [
                'company_id' => 1,
                'albums_category_name' => 'Видеоальбом',
                'albums_category_parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
            ],
            [
                'company_id' => 1,
                'albums_category_name' => 'Комментарии',
                'albums_category_parent_id' => null,
                'category_status' => 1,
                'author_id' => 1,
            ],

        ]);
    }
}
