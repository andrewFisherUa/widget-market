<?php 

Breadcrumbs::register('rekrut.statistic', function($breadcrumbs)
{
    $breadcrumbs->push('статистика', route('rekrut_product.statistic' ));
});

Breadcrumbs::register('rekrut.next.category', function($breadcrumbs)
{
    $breadcrumbs->push('поисковые категории', route('rekrut_product.next.category'));
});
Breadcrumbs::register('rekrut.next.category.rules', function($breadcrumbs,$category)
{
	$breadcrumbs->parent('rekrut.next.category');
    $breadcrumbs->push('правила категории '.$category->name, route('rekrut_product.next.category_rules',['id'=>$category->id]));
});
Breadcrumbs::register('rekrut.next.category.rule', function($breadcrumbs,$category)
{
	$breadcrumbs->parent('rekrut.next.category.rules',$category);
    $breadcrumbs->push('настройка правила категории '.$category->name, route('rekrut_product.next.category_rule',['id'=>0]));
});
Breadcrumbs::register('rekrut.next.category.addrule', function($breadcrumbs,$category)
{
	$breadcrumbs->parent('rekrut.next.category.rules',$category);
    $breadcrumbs->push('новое правило категории '.$category->name, route('rekrut_product.next.category_rule',['id'=>0]));
});
Breadcrumbs::register('rekrut.next.ycategories', function($breadcrumbs)
{
	#$breadcrumbs->parent('rekrut.next.category.rules',$category);
    $breadcrumbs->push('тест категорий ', route('rekrut_product.testis'));
});
Breadcrumbs::register('rekrut.next.ycategory.form', function($breadcrumbs,$category)
{
	$breadcrumbs->parent('rekrut.next.ycategories');
    $breadcrumbs->push('правило : '.$category->uniq_name, route('rekrut_product.next.category_form',['id'=>$category->id]));
});