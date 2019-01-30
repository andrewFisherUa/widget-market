<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
class VideoRenderController extends Controller
{
	public function index(Request $request){
		\Auth::user()->touch();
		$path=$request->path;
		$pathM=explode("/", $path);
		$pathId=explode("_", array_pop($pathM));
		$id=preg_replace('~[^0-9]+~','',array_shift($pathId));
		if ($id=='999999'){
			$block=\App\VideoBlock::findOrFail(1);
			$data=[];
			$data['id']=$id;
			$data['settings']['width']=550;
			$data['settings']['height']=350;
			$data['settings']['control']=0;
			$data['block']=$block->id;
			$data['ads']=$block->sources;
			return response()->json($data)->withHeaders(
			[
				'charset' => 'utf-8',
				'Access-Control-Allow-Origin' => '*'
			]);
		}
		$widget=\App\WidgetVideo::findOrFail($id);
		$wid=\App\MPW\Widgets\Widget::find($widget->wid_id);
		var_dump($wid);
		if ($wid->status==1){
			exit;
		}
		if (stripos($path, '/sng/')==false and stripos($path, '/mobile/')==false){
			if ($widget->on_rus==1){
				$wid=\App\MPW\Widgets\Widget::find($widget->wid_id);
				$pad=\App\PartnerPad::find($wid->pad);
				$filename=$_SERVER["DOCUMENT_ROOT"]."/video_blocks/".$id."_".$pad->domain.".json";
				if (is_file($filename)){
					exit;
				}
				else{
					$block=\App\VideoBlock::findOrFail($widget->block_rus);
					$data=[];
					$data['id']=$id;
					$data['settings']['width']=$widget->width;
					$data['settings']['height']=$widget->height;
					$data['settings']['control']=$widget->control;
					$data['block']=$block->id;
					$data['ads']=$block->sources;
					$new_data=json_encode($data);
					$path = $_SERVER["DOCUMENT_ROOT"]."/video_blocks/".$id."_".$pad->domain.".json";
					file_put_contents($path,$new_data);
					return response()->json($data)->withHeaders(
					[
						'charset' => 'utf-8',
						'Access-Control-Allow-Origin' => '*'
					]);
				}
			}
			else{
				return abort(403);
			}
		}
		elseif (stripos($path, '/sng/')==false and stripos($path, '/mobile/')==true){
			if ($widget->on_mobil==1){
				$wid=\App\MPW\Widgets\Widget::find($widget->wid_id);
				$pad=\App\PartnerPad::find($wid->pad);
				$filename=$_SERVER["DOCUMENT_ROOT"]."/video_blocks/mobile/".$id."_".$pad->domain.".json";
				if (is_file($filename)){
					exit;
				}
				else{
				if (!is_dir($_SERVER["DOCUMENT_ROOT"]."/video_blocks/mobile/")) {
					   mkdir($_SERVER["DOCUMENT_ROOT"]."/video_blocks/mobile/", 0777, true);
					}
					$block=\App\VideoBlock::findOrFail($widget->block_mobil);
					$data=[];
					$data['id']=$id;
					$data['settings']['width']=$widget->width;
					$data['settings']['height']=$widget->height;
					$data['settings']['control']=$widget->control;
					$data['block']=$block->id;
					$data['ads']=$block->sources;
					$new_data=json_encode($data);
					$path = $_SERVER["DOCUMENT_ROOT"]."/video_blocks/mobile/".$id."_".$pad->domain.".json";
					file_put_contents($path,$new_data);
					return response()->json($data)->withHeaders(
					[
						'charset' => 'utf-8',
						'Access-Control-Allow-Origin' => '*'
					]);
				}
			}
			else{
				return abort(403);
			}
		}
		elseif (stripos($path, '/sng/')==true and stripos($path, '/mobile/')==false){
			if ($widget->on_cis==1){
				$wid=\App\MPW\Widgets\Widget::find($widget->wid_id);
				$pad=\App\PartnerPad::find($wid->pad);
				$filename=$_SERVER["DOCUMENT_ROOT"]."/video_blocks/sng/".$id."_".$pad->domain.".json";
				if (is_file($filename)){
					exit;
				}
				else{
				if (!is_dir($_SERVER["DOCUMENT_ROOT"]."/video_blocks/sng/")) {
					   mkdir($_SERVER["DOCUMENT_ROOT"]."/video_blocks/sng/", 0777, true);
					}
					$block=\App\VideoBlock::findOrFail($widget->block_cis);
					$data=[];
					$data['id']=$id;
					$data['settings']['width']=$widget->width;
					$data['settings']['height']=$widget->height;
					$data['settings']['control']=$widget->control;
					$data['block']=$block->id;
					$data['ads']=$block->sources;
					$new_data=json_encode($data);
					$path = $_SERVER["DOCUMENT_ROOT"]."/video_blocks/sng/".$id."_".$pad->domain.".json";
					file_put_contents($path,$new_data);
					return response()->json($data)->withHeaders(
					[
						'charset' => 'utf-8',
						'Access-Control-Allow-Origin' => '*'
					]);
				}
			}
			else{
				return abort(403);
			}
		}
	}
}
