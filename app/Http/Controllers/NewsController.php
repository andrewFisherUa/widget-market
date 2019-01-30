<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use App\News;
use Charts;
use App\Notifications\NewNews;
class NewsController extends Controller
{
    public function addNews(){
		\Auth::user()->touch();
		return view('news.add_news');
	}
	public function index(Request $request){
		\Auth::user()->touch();
		$important[]=$request->input('important');
		$imp=$important[0];
		if (empty($important[0])){
			$important=[1,2,3];
			$imp=0;
		}
		$type=$request->input('type');
		if (!$type){
			$type=[1,2,3,4];
		}
		$role=$request->input('role');
		if (!$role){
			$role=[1,2];
		}
		if (Auth::user()->hasRole('affiliate')){
		$news=News::where('role', 1)->whereIn('important', $important)->whereIn('type', $type)->orderBy('created_at', 'desc')->paginate(20);
		}
		else if(Auth::user()->hasRole('advertiser')){
		$news=News::where('role', 2)->whereIn('important', $important)->whereIn('type', $type)->orderBy('created_at', 'desc')->paginate(20);
		}
		else{
		$news=News::whereIn('important', $important)->whereIn('type', $type)->whereIn('role', $role)->orderBy('created_at', 'desc')->paginate(20);
		}
		
		return view('news.index', ['news'=>$news, 'imp'=>$imp, 'type'=>$type, 'role'=>$role]);
	}
	public function showOne($id){
		\Auth::user()->touch();
		$news=News::findOrFail($id);
	
		if ($news->role!=Auth::user()->roles['0']->id and !Auth::user()->hasRole('admin') and !Auth::user()->hasRole('manager') and !Auth::user()->hasRole('super_manager')){
			return abort(404);
		}
		if (Auth::user()->notifications->where('type', 'App\Notifications\NewNews')->where('notifiable_id',$id)){
			$notification=Auth::user()->notifications->where('type', 'App\Notifications\NewNews')->where('data',$id)->first(); 
			if (isset($notification)){
				$notification->markAsRead();
			}
		}
		if (Auth::user()->hasRole('affiliate')){
		$news_lim=News::where('role', 1)->orderBy('created_at', 'desc')->take(10)->get();
		}
		else if(Auth::user()->hasRole('advertiser')){
		$news_lim=News::where('role', 2)->orderBy('created_at', 'desc')->take(10)->get();
		}
		else{
		$news_lim=News::orderBy('created_at', 'desc')->take(10)->get();
		}
		return view('news.show_one_news', ['news'=>$news, 'news_lim'=>$news_lim]);
	}
	public function edit($id){
		\Auth::user()->touch();
		$news=News::findOrFail($id);
		return view('news.edit_news', ['news'=>$news]);
	}
	public function save(Request $request){
		\Auth::user()->touch();
		$news = new News;
		$news->role=$request->input('role');
		$news->important=$request->input('important');
		$news->type=$request->input('type');
		$news->header=$request->input('header');
		$news->anoun=$request->input('anoun');
		$news->body=$request->input('body');
		$news->save();
		if ($news->role==1){
			$users=User::whereHas('roles', function ($query) {
			$query->where('id', 1);
			})->where('unsubscribe', '0')->orWhere('id', 1)->orderBy('id', 'desc')->get();
			
		}
		if ($news->role==2){
			$users=User::whereHas('roles', function ($query) {
			$query->where('id', 2);
			})->where('unsubscribe', '0')->orWhere('id', 1)->orderBy('id', 'desc')->get();
		}
		$when = \Carbon\Carbon::now()->addSecond(10);
        \Notification::send($users, (new NewNews($news->id))->delay($when));
		return redirect('/news')->with('message_success', "Новость № $news->id '$news->header' сохранена и отправлена на email адреса.");
	}
	public function update(Request $request,$id){
		\Auth::user()->touch();
		$news=News::findOrFail($id);
		$news->role=$request->input('role');
		$news->important=$request->input('important');
		$news->type=$request->input('type');
		$news->header=$request->input('header');
		$news->anoun=$request->input('anoun');
		$news->body=$request->input('body');
		$news->save();
		return redirect('/news')->with('message_success', "Новость № $id '$news->header' отредактирована.");
	}
	public function delete($id){
		\Auth::user()->touch();
		$news=News::findOrFail($id);
		$title=$news->header;
		$news->delete();
		$nots=\Notification::where('data', $id)->get();
		foreach ($nots as $not){
			$not->delete();
		}
		return redirect('/news')->with('message_success', "Новость № $id '$title' удалена.");
	}
	public function readAll(){
		\Auth::user()->touch();
		$notifs=Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewNews');
		foreach ($notifs as $notif){
			$notif->markAsRead();
		}
		return redirect('/news')->with('message_success', "Все новости отмечены как прочитанные");
	}
	public function unsubscribe(){
		\Auth::user()->touch();
		$users=\App\User::where('unsubscribe', '1')->get();
		return view('news.unsubscribe', ['users'=>$users]);
	}
}
