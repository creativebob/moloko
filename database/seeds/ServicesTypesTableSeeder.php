<?php

use Illuminate\Database\Seeder;

class ServicesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('services_types')->insert([
        	[
		        'name' => 'Воздействие на тело человека',
		        'description' => 'Услуги, направленные на тело человека',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Воздействие на материальный объект',
		        'description' => 'Услуги, направленные на физические объекты, находящиеся в собственности человека',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Воздействие на сознание человека',
		        'description' => 'Услуги, направленные на сознание человека',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        	[
		        'name' => 'Нематериальный актив',
		        'description' => 'Услуги, направленные на нематериальные актив',
		        'company_id' => null,
                'author_id' => 1,
                'system_item' => 1,
                'moderation' => null,
        	],
        ]);
    }
}
