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
class VideoSettingsController extends Controller
{
	public function allSources(Request $request){
		\Auth::user()->touch();
		$sql='select * from videoplayer';
		$pdo = \DB::connection('videotest')->getPdo();
		$players = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$player=$request->input('player');
		$category=$request->input('category');

		if ($request->title){

		$sources=VideoSource::select("links.id","links.title","links.player as player_id","links.color","links.cheap","links.category","videoplayer.title as player")
		         
                         ->leftJoin('videoplayer', function($join)
                         {
                             $join->on( 'links.player', '=', 'videoplayer.id');
                             //$join->on('links_summa_cis.country','=',\DB::raw("'CIS'"));
							// $join->on('links_summa_cis.day','=',\DB::raw("'".date("Y-m-d")."'"));

                         })	->where('links.title', 'ilike', '%'.$request->title.'%')->where('links.status', '1')->get();
		}
		else{
		
			$sources=VideoSource::select("links.id","links.title","links.player","links.color","links.cheap","links.category","videoplayer.title as player")
			->leftJoin('videoplayer', function($join)
                         {
                             $join->on( 'links.player', '=', 'videoplayer.id');
                             //$join->on('links_summa_cis.country','=',\DB::raw("'CIS'"));
							// $join->on('links_summa_cis.day','=',\DB::raw("'".date("Y-m-d")."'"));

                         })						 
			->where(function ($query) use ($player){
                    if ($player=='all' or !$player){
					
				    }
					else if($player=='line'){
					$query->whereIn('player', [0])->orWhereNull('player');
				}
				else{
					$query->where('player', $player);
				}
			})
			->where(function ($query) use ($category){
                    if ($category=='all' or !$category){
					
				    }
					else if($category=='white'){
					$query->whereIn('category', [0])->orWhereNull('category');
				}
				else if($category=='adult'){
					$query->whereIn('category', [1]);
				}
				else if($category=='razv'){
					$query->whereIn('category', [2]);
				}
			})->where('links.status', '1')
			->orderBy('title', 'asc')->get(); 
	
		}
		return view('admin.video_settings.sources', ['category'=>$category, 'players'=>$players, 'sources'=>$sources, 'title'=>$request->title, 'player'=>$player]);
	}
	
	public function allSourcesDefolte(){
		$sources=VideoSource::where('status', 1)->orderBy('title', 'asc')->get();
		return view('admin.video_settings.sources_default', ['sources'=>$sources]);
	}
	
	public function allSourcesDefoltePost(Request $request){
		$ids=$request->input('id');
		$rus=$request->input('rus');
		$cis=$request->input('cis');
		$sources=[];
		foreach ($ids as $key=>$id){
			$sources[$id]['rus']=$rus[$key];
			$sources[$id]['cis']=$cis[$key];
		}
		foreach ($sources as $key=>$s){
			$source=VideoSource::findOrFail($key);
			$source->summa_rus=$s['rus'];
			$source->summa_cis=$s['cis'];
			$source->save();
		}
		return back()->with('message_success', "Цены успешно изменены.");
	}
	
	public function editSource($id){
		\Auth::user()->touch();
		$source=VideoSource::findOrFail($id);
		$players=\DB::connection("videotest")->table("videoplayer")->orderBy("title")->get();
		return view('admin.video_settings.edit_source', ['source'=>$source,"players"=>$players]);
	}
	
	public function updateSource($id, Request $request){

		\Auth::user()->touch();
		$source=VideoSource::findOrFail($id);
		$source->title=$request->input('title');
		$source->src=$request->input('src');
		if ($request->input('player')==0){
			$source->player=null;
		}
		else{
			$source->player=$request->input('player');
		}
		//$source->limit=$request->input('limit');
		//$source->timeout=$request->input('timeout');
		if (!$request->input('cheap')){
			$cheap=0;
		}
		else{
			$cheap=1;
		}
                $parallel=$request->input('parallel',0);
		$source->cheap=$cheap;
		$source->parallel=$parallel;
		$source->category=$request->input('category');
		$source->partner_script=$request->input('partner_script');
		$source->summa_rus=$request->input('summa_rus');
		$source->summa_cis=$request->input('summa_cis');
		$source->limit=$request->input('limit');
		$source->timeout=$request->input('timeout');
		$source->ftime=$request->input('ftime');
		$source->color=$request->input('color');
		$source->coment=$request->input('coment');
		$source->save();

        \App\Videosource\DiscUtil::Utile();

		return redirect('video_sources')->with('message_success', "Ссылка $source->title успешно изменена.");
	}
	
	public function deleteSource($id){
		\Auth::user()->touch();
		$source=VideoSource::findOrFail($id);
		$source->status=0;
		$source->save();
		\App\Videosource\DiscUtil::Utile();
		return redirect('video_sources')->with('message_success', "Ссылка $source->title успешно удалена.");
	}
	
	public function addSource(){
		\Auth::user()->touch();
		$sql='select * from videoplayer';
		$pdo = \DB::connection('videotest')->getPdo();
		$players = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		return view('admin.video_settings.add_source', ['players'=>$players]);
	}
	
	public function addSourceNew(Request $request){
		\Auth::user()->touch();
		$source=new VideoSource;
		$source->src=$request->input('src');
		$source->player=$request->input('player');
		if (!$request->input('cheap')){
			$cheap=0;
		}
		else{
			$cheap=1;
		}
		$source->cheap=$cheap;
		$source->title=$request->input('title');
		$source->summa_rus=$request->input('summa_rus');
		$source->summa_cis=$request->input('summa_cis');
		$source->color=$request->input('color');
		$source->partner_script=$request->input('partner_script');
		$source->category=$request->input('category');
		$source->limit=$request->input('limit');
		$source->timeout=$request->input('timeout');
		$source->ftime=$request->input('ftime');
		$source->save();
		return redirect('video_sources')->with('message_success', "Ссылка $source->title успешно создана.");
	}
	
	public function blockCreate(){
		\Auth::user()->touch();
		$sources=VideoSource::select("links.id","links.title","links.player","links.color","links.cheap","links.category","videoplayer.title as player")->leftJoin('videoplayer', function($join)
            {
              $join->on( 'links.player', '=', 'videoplayer.id');
			})->where('links.status', '1')->orderBy('title', 'asc')->get();
		return view('admin.video_settings.create_block', ['sources'=>$sources]);
	}
	
	public function blocksAll(Request $request){
		\Auth::user()->touch();
		$from=$request->input('from');
		$to=$request->input('to');
		if(!($from||$to)){
			$from=$to=date('Y-m-d');
        }
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"calculated";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		
		$header=[
            ['title'=>"Название",'index'=>"name","order"=>"",'url'=>""],
			['title'=>"Запросы",'index'=>"requested","order"=>"",'url'=>""],
			['title'=>"Запросы с мин. 1 проигрыванием",'index'=>"calculated","order"=>"",'url'=>""],
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }

        $render=array('header'=>$header,'order'=>$order,'direct'=>$direct);
		$blocks=VideoBlock::select('blocks.id', 'blocks.name', \DB::raw('coalesce(SUM(block_summa.requested),0) as requested'), \DB::raw('coalesce(SUM(block_summa.calculated),0) as calculated'))->leftJoin('block_summa', function($join) use ($from,$to)
            {
              $join->on( 'blocks.id', '=', 'block_summa.id_block')->whereBetween('block_summa.day', array($from, $to));
			})->groupBy('blocks.id', 'blocks.name')->orderBy($order,$direct)->get();
		return view('admin.video_settings.blocks', ['from'=>$from, 'to'=>$to, 'blocks'=>$blocks, 'header'=>$header]);
	}
	
	public function blockEdit($id){
		\Auth::user()->touch();

		
		$block=VideoBlock::findOrFail($id);
		$sources=VideoSource::select("links.id","links.title","links.player","links.color","links.cheap","links.category","videoplayer.title as player")->leftJoin('videoplayer', function($join)
            {
              $join->on( 'links.player', '=', 'videoplayer.id');
			})->where('links.status', '1')
			//->orderBy('links.sort', 'asc')->get();
			->orderBy('title', 'asc')->get();
		return view('admin.video_settings.edit_block', ['block'=>$block, 'sources'=>$sources]);
	}
	
	public function blockUpdate($id, Request $request){
		\Auth::user()->touch();
       
		$block=VideoBlock::findOrFail($id);
		$urls=$request->input('url');
		$type=$request->input('type');
		#var_dump($type); die();
		$urlsPrioritet=$request->input('prioritet');
		$links=[];
		if ($request->input('autosort')){
			$autosort=1;
		}
		else{
			$autosort=0;
		}
		foreach($urls as $id=>$url){
			if($url){
				$links[$id]['url']=$url;
				foreach($urlsPrioritet as $idp=>$prioritet){
					if ($id==$idp){
						if ($autosort==1){
							$links[$id]['prioritet']=$prioritet;
						}
						else{
						$links[$id]['prioritet']=0;
						}
					}
				}
			}
		}
		foreach($urlsPrioritet as $id=>$url){
				$prioritet[$id]=$url;
		}
		$block->name=$request->input('title');
		if ($request->input('cheap_util')){
			$cheap_util=1;
		}
		else{
			$cheap_util=0;
		}
		$block->autosort=$autosort;
		$block->repeat=$request->input('repeat');
		$block->cheap_util=$cheap_util;
		$block->type=$type;
		$block->save();
		\DB::connection('videotest')->table('blocks_links')->where('id_block', $block->id)->delete();
		$cache=[];
		foreach($links as $key=>$link){
		    if($link['url']  && !isset($cache[$link['url']])){
			$cache[$link['url']]=1;
			$block->Sources()->attach($link['url'], ["sort"=>$key, "prioritet"=>$link['prioritet']]);
			
			}
		}
		//$block->saveOptionsFile(json_encode($block->sources));
	     $cmd = "/bin/rm -rf ".$_SERVER["DOCUMENT_ROOT"]."/video_blocks/* ";
    	`$cmd`;
		 \App\Videosource\DiscUtil::Utile();
		 \Artisan::call('statistic:util');
		return back()->with('message_success', "Блок $block->name успешно изменен.");
	}
	
	public function blockSave(Request $request){
		\Auth::user()->touch();
		$urls=$request->input('url');
		$links=[];
		foreach($urls as $id=>$url){
			if($url){
				$links[$id]=$url;
			}
		}
		$block=new VideoBlock();
		$block->name=$request->input('title');
		$block->save();
		foreach($links as $key=>$link){
			$block->Sources()->attach($link, ["sort"=>$key]);
		}
		/*$block->saveOptionsFile(json_encode($block->sources));*/
		return redirect('video_block')->with('message_success', "Блок $block->name успешно создан.");
	}
	
	public function default(){
		\Auth::user()->touch();
		$defaults=\App\VideoDefault::orderBy('id', 'asc')->get();
		return view('admin.video_settings.default', ['defaults'=>$defaults]);
	}
	
	public function defaultOne($id){
		\Auth::user()->touch();
		$default=\App\VideoDefault::where('id', $id)->first();
		$blocks=\App\VideoBlock::all();
		$commissions=\DB::table('сommission_groups')->where('type', '3')->get();
		return view('admin.video_settings.default_one', ['default'=>$default, 'blocks'=>$blocks, 'commissions'=>$commissions]);
	}
	
	public function defaultSave(Request $request){
		\Auth::user()->touch();
		$id=$request->input('id');
		$default=\App\VideoDefault::find($id);
		$default->block_rus=$request->input('block_rus');
		$default->block_mobile=$request->input('block_mobile');
		$default->block_cis=$request->input('block_cis');
		$default->commission_rus=$request->input('commission_rus');
		$default->commission_cis=$request->input('commission_cis');
		$default->save();
		return redirect('video_settings_default')->with('message_success', "Блок $default->name успешно изменен.");
	}
}
