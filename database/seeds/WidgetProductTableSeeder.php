<?php

use Illuminate\Database\Seeder;

class WidgetProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$idslider=DB::table('widget_product_types')->insertGetId([
            'name' => 'slider',
            'title' => 'Слайдер'
        ]);
		$idmodule=DB::table('widget_product_types')->insertGetId([
            'name' => 'module',
            'title' => 'Модульный'
        ]);
		$idtable=DB::table('widget_product_types')->insertGetId([
            'name' => 'table',
            'title' => 'Таблица'
        ]);
		$idmobile=DB::table('widget_product_types')->insertGetId([
            'name' => 'mobile',
            'title' => 'Мобильный'
        ]);
		DB::table('widget_product_templates')->insert([
            'type' => $idslider,
			'name' => 'block-mono',
            'title' => 'Стандартный слайдер'
        ]);
		DB::table('widget_product_templates')->insert([
            'type' => $idmodule,
			'name' => 'module-block-third-1',
            'title' => 'Квадратные блоки'
        ]);
		DB::table('widget_product_templates')->insert([
            'type' => $idtable,
			'name' => 'table-mini',
            'title' => 'Таблица'
        ]);
    }
}
