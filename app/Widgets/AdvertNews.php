<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use App\News;
class AdvertNews extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
		
      if($this->config["user"]->hasRole('affiliate')){
		$news=News::where('role', 1)->orderBy('created_at', 'desc')->get()->take(10);
		}elseif($this->config["user"]->hasRole('advertiser')){
		$news=News::where('role', 2)->orderBy('created_at', 'desc')->get()->take(10);
		}else{
		$news=News::orderBy('created_at', 'desc')->get()->take(10);
		}
		#$news=News::where('role', 1)->orderBy('created_at', 'desc')
		#var_dump($news);
		#$news=News::orderBy('created_at', 'desc')->paginate(20);
		
		
        return view('widgets.advert_news', [
            'config' => $this->config,"news"=>$news
        ]);
    }
}
