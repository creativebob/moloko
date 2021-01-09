<?php

use Illuminate\Database\Seeder;

use App\ToolsType;

class ToolsTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ToolsType::insert([
            [
                'name' => 'Торгово-кассовое оборудование',
                'description' => null,
                'alias' => 'cash-equipment'
            ],
        ]);
    }
}
