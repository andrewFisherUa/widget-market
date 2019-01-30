<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WidgetTizer extends Model
{
	protected $table='widget_tizers';
	protected $fillable = [
        'wid_id'
    ];
	public function hasAttribute($attr)
    {
    return isset($this->$attr);
    }
   public function widgetSave($parameters){
   
   }
   public function mobile_render(){
		$body=$this->getTemplateMobile();
		$style=$this->getStyleMobile();
		 $script=$this->getScriptMobile();
		 if(!$this->name_mobile){
	$name="block-mobile";
	}
	else{
	$name=$this->name_mobile;
	}
	if(!$this->width || !$this->height){
		    $width="200px";
			$height="200px";
	      }
	      else{
	        $width=$this->width;
			$height=$this->height;
	      }
	if (!$this->mobile_background){
			$background='rgba(255,255,255,1)';
		 }
		 else{
			$background=$this->mobile_background;
		 }
	if (!$this->background_model){
			$background_model='rgba(255,255,255,1)';
		 }
		 else{
			$background_model=$this->background_model;
		 }
	if (!$this->background_model_hover){
			$background_model_hover='rgba(255,255,255,1)';
		 }
		 else{
			$background_model_hover=$this->background_model_hover;
		 }
	if (!$this->mobile_font_family){
			$mobile_font_family='ArialRegular';
		 }
		 else{
			$mobile_font_family=$this->mobile_font_family;
		 }
		 $res=["body"=>$body, "style"=>$style, "script"=>$script, "name"=>$name, "width"=>$width, "height"=>$height, "background"=>$background, "background_model"=>$background_model, "background_model_hover"=>$background_model_hover, 'mobile_font_family'=>$mobile_font_family];
    return $res;
   }
   public function render(){
      
   		 $body=$this->getTemplate();
		 
		 $style=$this->getStyle();
		 $script=$this->getScript();
		 /*
   		 if (!isset($parameters["name"])){
		 $name="block-mono";
		}
		else{
			$name=$parameters["name"];
		}
		*/
	if(!$this->name){
	$name="module-block";
	}
	else{
	$name=$this->name;
	}
		 if($this->width===null || $this->height ===null){
		    $width="200px";
			$height="200px";
	      }
	      else{
	        $width=$this->width;
			$height=$this->height;
	      }
		  if ($this->width=='0'){
			$width='0';
		  }
		  if ($this->height=='0'){
			$height='0';
		  }
		 if (!$this->cols){
			$cols=1;
		 }
		 else{
			$cols=$this->cols;
		 }
		 if (!$this->row){
			$row=1;
		 }
		 else{
			$row=$this->row;
		 }
		 if (!$this->background){
			$background='rgba(255,255,255,1)';
		 }
		 else{
			$background=$this->background;
		 }
		 if (!$this->border_color){
			$border_color='rgba(0, 0, 0, .1)';
		 }
		 else{
			$border_color=$this->border_color;
		 }
		 if (!$this->border_width){
			$border_width=1;
		 }
		 else{
			$border_width=$this->border_width;
		 }
		 if (!$this->border_radius){
			$border_radius=1;
		 }
		 else{
			$border_radius=$this->border_radius;
		 }
		 if (!$this->background_model){
			$background_model='rgba(255,255,255,1)';
		 }
		 else{
			$background_model=$this->background_model;
		 }
		 if (!$this->background_model_hover){
			$background_model_hover='rgba(255,255,255,1)';
		 }
		 else{
			$background_model_hover=$this->background_model_hover;
		 }
		 if (!$this->font_family){
			$font_family='ArialRegular';
		 }
		 else{
			$font_family=$this->font_family;
		 }
		 if (!$this->font_size){
			$font_size=1;
		 }
		 else{
			$font_size=$this->font_size;
		 }
		 
		 
		  
		 $res=["font_size"=>$font_size, "font_family"=>$font_family, "background_model_hover"=>$background_model_hover, "background_model"=>$background_model, "border_radius"=>$border_radius, "border_width"=>$border_width, "border_color"=>$border_color, "background"=>$background, "name"=>$name, "body"=>$body, "style"=>$style, "script"=>$script, "width"=>$width, "height"=>$height, "cols"=>$cols, "row"=>$row];
    return $res;
   }
   	public function getTemplate(){
	/*
	if(!isset($parameters["name"])){
	$name="block-mono";
	}
	else{
	$name=$parameters["name"];
	}
	*/
	if(!$this->name){
	$name="module-block";
	}
	else{
	$name=$this->name;
	}
    $b="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/body.html";
	if(!is_file($b)){
	echo "незабуду !!!";  exit();
	}
	$body=file_get_contents($b);
	return $body;
	}
	
	public function getTemplateMobile(){
	if(!$this->name_mobile){
	$name="block-mobile";
	}
	else{
	$name=$this->name_mobile;
	}
	//var_dump($name);
    $b="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/body.html";
	if(!is_file($b)){
	echo "Не нашел мобильный боди !!!";  exit();
	}
	$body=file_get_contents($b);
	return $body;
	}
	
	public function getStyle(){
	/*
	if(!isset($parameters["name"])){
	$name="block-mono";
	}
	else{
	$name=$parameters["name"];
	}
	*/
	if(!$this->name){
	$name="module-block";
	}
	else{
	$name=$this->name;
	}
    $css="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/css/widget-slider-big.css";
	//var_dump($css); die();
	if(!is_file($css)){
	echo "незабуду1 !!!";  exit();
	}
	$style=file_get_contents($css);
	return $style;
	}
	
	public function getStyleMobile(){
	if(!$this->name_mobile){
	$name="block-mobile";
	}
	else{
	$name=$this->name_mobile;
	}
    $css="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/css/widget-slider-big.css";
	//var_dump($css); die();
	if(!is_file($css)){
	echo "Не нашел стили мобильного !!!";  exit();
	}
	$style=file_get_contents($css);
	return $style;
	}
	
	public function getScript(){
	if(!$this->name){
	$name="widget-module-block";
	}
	else{
	$name=$this->name;
	}
    $s="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/js/~init.js";
	if(!is_file($s)){
	echo "незабуду !!!";  exit();
	}
	$script=file_get_contents($s);
	return $script;
	}
	
	public function getScriptMobile(){
	if(!$this->name_mobile){
	$name="block-mobile";
	}
	else{
	$name=$this->name_mobile;
	}
    $s="/home/mp.su/api.market-place.su/widget_tizer/templates/widget-".$name."/js/~init.js";
	if(!is_file($s)){
	echo "не нашел скрипты мобильные !!!";  exit();
	}
	$script=file_get_contents($s);
	return $script;
	}
}
