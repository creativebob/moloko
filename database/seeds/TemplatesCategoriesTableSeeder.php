<?php

use Illuminate\Database\Seeder;
use App\TemplatesCategory;

class TemplatesCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        TemplatesCategory::insert([
            [
                'name' => 'Email шаблоны',
                'slug' => \Str::slug('Email шаблоны'),
                'level' => 1,
                'company_id' => null,
                'author_id' => 1,
                'display' => true,
            ],
        ]);
    }
}
