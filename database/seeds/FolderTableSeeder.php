<?php

use Illuminate\Database\Seeder;

class FolderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('folders')->insert([
        	[
		        'folder_name' => 'Компании',
		        'folder_alias' => 'companies',
		        'folder_url' => 'public/app/companies',
		        'folder_parent_id' => null,
        	],
        	[
		        'folder_name' => 'Филиалы',
		        'folder_alias' => 'filials',
		        'folder_url' => 'public/app/companies/filials',
		        'folder_parent_id' => 1,
        	],
        	[
		        'folder_name' => 'Пользователи',
		        'folder_alias' => 'users',
		        'folder_url' => 'public/app/companies/filials/users',
		        'folder_parent_id' => 2,
        	],
        	[
		        'folder_name' => 'Сайты',
		        'folder_alias' => 'sites',
		        'folder_url' => 'public/app/companies/filials/sites',
		        'folder_parent_id' => 2,
        	],
        	[
		        'folder_name' => 'Товары',
		        'folder_alias' => 'goods',
		        'folder_url' => 'public/app/companies/filials/goods',
		        'folder_parent_id' => 2,
        	],
        	[
		        'folder_name' => 'Документы',
		        'folder_alias' => 'docs',
		        'folder_url' => 'public/app/companies/filials/docs',
		        'folder_parent_id' => 2,
        	],
        	[
		        'folder_name' => 'Аватары',
		        'folder_alias' => 'avatars',
		        'folder_url' => 'public/app/companies/filials/users/avatars',
		        'folder_parent_id' => 3,
        	],
        	[
		        'folder_name' => 'Фотографии',
		        'folder_alias' => 'photos',
		        'folder_url' => 'public/app/companies/filials/users/photos',
		        'folder_parent_id' => 3,
        	],
        	[
		        'folder_name' => 'Видео',
		        'folder_alias' => 'video',
		        'folder_url' => 'public/app/companies/filials/users/video',
		        'folder_parent_id' => 3,
        	],

        ]);
    }
}
