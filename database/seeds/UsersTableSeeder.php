<?php

use Illuminate\Database\Seeder;
use \App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$affiliate = new Role();
		$affiliate->name='affiliate';
		$affiliate->display_name='Партнер';
		$affiliate->description='Владельцы сайтов';
		$affiliate->save();
		
		$role = new Role();
		$role->name='advertiser';
		$role->display_name='Рекламодатель';
		$role->description='Рекламодатели';
		$role->save();
		
		$manager = new Role();
		$manager->name='manager';
		$manager->display_name='Менеджер';
		$manager->description='Менеджеры';
		$manager->save();
		
		$manager = new Role();
		$manager->name='super_manager';
		$manager->display_name='Старший менеджер';
		$manager->description='Старшие менеджеры';
		$manager->save();
		
		$admin = new Role();
		$admin->name='admin';
		$admin->display_name='Андминистратор';
		$admin->description='Администратор сервера';
		$admin->save();
		
        $idfirst=DB::table('users')->insertGetId([
            'name' => 'Сергей Чага',
            'email' => 'chagasergey1501@gmail.com',
            'password' => bcrypt('18745037'),
			'status' => '1'
        ]);
		$idsecond=DB::table('users')->insertGetId([
            'name' => 'Николай Окулов',
            'email' => 'teleprokat@gmail.com',
            'password' => bcrypt('dribidu'),
			'status' => '1'
        ]);
		DB::table('user_profiles')->insert([
			'user_id' => $idfirst,
            'name' => 'Сергей Чага',
            'firstname' => 'Сергей',
			'lastname' => 'Чага',
			'email' => 'chagasergey1501@gmail.com'
        ]);
		DB::table('user_profiles')->insert([
			'user_id' => $idsecond,
            'name' => 'Николай Окулов',
            'firstname' => 'Николай',
			'lastname' => 'Окулов',
			'email' => 'teleprokat@gmail.com'
        ]);
		$default=\App\Role::where('name','=','affiliate')->first();
		\App\User::find($idfirst)->attachRole($default);
		\App\User::find($idsecond)->attachRole($default);

    }
}
