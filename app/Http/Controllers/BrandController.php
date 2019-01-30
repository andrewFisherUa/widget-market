<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\PartnerPad;
use Illuminate\Support\Facades\Validator;
use App\VideoSource;
use App\VideoBlock;
use Illuminate\Console\Command;
use Intervention\Image\ImageManagerStatic as Image;
class BrandController extends Controller
{
	public function addSource(Request $request){
		\Auth::user()->touch();
		return view('admin.brand.add_source');
	}
	
	public function postAddSource(Request $request){
		\Auth::user()->touch();
		$title=$request->input('title');
		$src=$request->input('src');
		$img=$request->file('img');
		$summa_rus=$request->input('summa_rus')?$request->input('summa_rus'):0;
		$summa_cis=$request->input('summa_cis')?$request->input('summa_cis'):0;
		$validator=Validator::make(
			array(
				'title' => $title,
				'src' => $src,
				'img'=> $img
			),
			array(
				'title' => 'required|string|max:255',
				'src' => 'required|max:1000',
				'img' => 'image|max:1024|mimes:jpeg,png,gif',
			)
		);
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}
		$brand=new \App\BrandOffer;
		$brand->title=$title;
		$brand->src=$src;
		$brand->summa_rus=$summa_rus;
		$brand->summa_cis=$summa_cis;
		$brand->save();
		
		$filename = $brand->id . '.' . $img->getClientOriginalExtension();
		$filedirect='/home/www/storage.market-place.su/brand_img';
		Image::make($img)->save($filedirect.'/'.$filename);
		$brand->img=$filename;
		$brand->save();
		return redirect()->route('brand_setting.all.source');
	}
	
	public function allSource(){
		\Auth::user()->touch();
		$brands=\App\BrandOffer::where('status', 0)->orderBy('title', 'asc')->get();
		return view('admin.brand.all_source', ['brands'=>$brands]);
	}
	
	public function addBlock(){
		\Auth::user()->touch();
		$sources=\App\BrandOffer::where('status', 0)->orderBy('title', 'asc')->get();
		return view('admin.brand.add_block', ['sources'=>$sources]);
	}
	
	public function postAddBlock(Request $request){
		\Auth::user()->touch();
		$title=$request->input('title');
		$urls=$request->input('url');
		$links=[];
		foreach($urls as $id=>$url){
			if($url){
				$links[$id]=$url;
			}
		}
		$block=new \App\BrandBlock();
		$block->title=$request->input('title');
		$block->save();
		foreach($links as $key=>$link){
			$block->Sources()->attach($link, ["sort"=>$key]);
		}
		return redirect()->route('brand_setting.all.block')->with('message_success', "Блок $block->name успешно создан.");
	}
	
	public function editSource($id){
		\Auth::user()->touch();
		$source=\App\BrandOffer::findOrFail($id);
		return view('admin.brand.edit_source', ['source'=>$source]);
	}
	
	public function postEditSource($id, Request $request){
		\Auth::user()->touch();
		$title=$request->input('title');
		$src=$request->input('src');
		$img=$request->file('img');
		$summa_rus=$request->input('summa_rus')?$request->input('summa_rus'):0;
		$summa_cis=$request->input('summa_cis')?$request->input('summa_cis'):0;
		$validator=Validator::make(
			array(
				'title' => $title,
				'src' => $src,
			),
			array(
				'title' => 'required|string|max:255',
				'src' => 'required|max:1000',
			)
		);
		if ($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}
		$brand=\App\BrandOffer::findOrFail($id);
		$brand->title=$title;
		$brand->src=$src;
		$brand->summa_rus=$summa_rus;
		$brand->summa_cis=$summa_cis;
		if ($img){
			$validator=Validator::make(
				array(
					'title' => $title,
					'src' => $src,
					'img'=> $img
				),
				array(
					'title' => 'required|string|max:255',
					'src' => 'required|max:1000',
					'img' => 'image|max:1024|mimes:jpeg,png,gif',
				)
			);
			if ($validator->fails()){
				return back()->withErrors($validator)->withInput();
			}
			$filename = $brand->id . '.' . $img->getClientOriginalExtension();
			$filedirect='/home/www/storage.market-place.su/brand_img';
			Image::make($img)->save($filedirect.'/'.$filename);
			$brand->img=$filename;
		}
		$brand->save();
		\App\Videosource\DiscUtil::BrandUtile();
		return back()->with('message_success', "Ссылка успешно изменена.");
	}
	
	public function allBlock(){
		\Auth::user()->touch();
		$blocks=\App\BrandBlock::where('status', 0)->orderBy('title', 'asc')->get();
		return view('admin.brand.all_block', ['blocks'=>$blocks]);
	}
	
	public function DeleteSource($id){
		\Auth::user()->touch();
		$source=\App\BrandOffer::findOrFail($id);
		$source->status=1;
		$source->save();
		\App\Videosource\DiscUtil::BrandUtile();
		return back()->with('message_success', "Ссылка $source->title успешно удалена.");
	}
	
	public function editBlock($id){
		\Auth::user()->touch();
		$block=\App\BrandBlock::findOrFail($id);
		$sources=\App\BrandOffer::where('status', 0)->orderBy('title', 'asc')->get();
		return view('admin.brand.edit_block', ['block'=>$block, 'sources'=>$sources]);
	}
	
	public function postEditBlock($id, Request $request){
		\Auth::user()->touch();
		$block=\App\BrandBlock::findOrFail($id);
		$block->title=$request->input('title');
		$urls=$request->input('url');
		$links=[];
		foreach($urls as $id=>$url){
			if($url){
				$links[$id]=$url;
			}
		}
		\DB::table('brand_block_links')->where('id_block', $block->id)->delete();
		foreach($links as $key=>$link){
			$block->Sources()->attach($link, ["sort"=>$key]);
		}
		\App\Videosource\DiscUtil::BrandUtile();
		return back()->with('message_success', "Блок $block->name успешно изменен.");
	}
	
	public function deleteBlock($id){
		\Auth::user()->touch();
		$block=\App\BrandBlock::findOrFail($id);
		$block->status=1;
		$block->save();
		\DB::table('brand_block_links')->where('id_block', $block->id)->delete();
		\App\Videosource\DiscUtil::BrandUtile();
		return back()->with('message_success', "Блок $block->name успешно удален.");
	}
}
