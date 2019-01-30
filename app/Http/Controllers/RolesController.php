<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\Role;
class RolesController extends Controller
{
	public function Roles(){
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
	}
}
