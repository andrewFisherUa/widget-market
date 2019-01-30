<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WidgetRewriteTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widget:rewritetemplate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	$widgets=\App\MPW\Widgets\Widget::where("type","=",1)->get();
	foreach($widgets as $widget){
	$wid=\App\WidgetEditor::where('wid_id',$widget->id)->first();
	
	if(!$wid) continue;
	    $args=$wid->render();
		
		$path = "/home/www/precluck.market-place.su/public/compiled/widget_".$wid->wid_id.".html";
		$conf = view('widget.render',$args);
        file_put_contents($path, $conf);
	     print $wid->wid_id."\n";
	}
        
    }
}
