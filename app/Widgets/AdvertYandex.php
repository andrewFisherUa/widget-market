<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class AdvertYandex extends AbstractWidget
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
		$selecte=[];
		 if($this->config["id"]){
			 
		$sql="select * from advertises_yandex where advertise_id = ".$this->config["id"]."";	 
		$ss=\DB::connection("advertise")->select($sql);
		foreach($ss as $s){
			$selecte[$s->yandex_id]=1;
			
		}
		
		 }
		 
		
		$collection=[];
        $sql="select * from yandex_categories where id_parent=90401 order by name";
		$data=\DB::connection("cluck")->select($sql);
		foreach($data as $d){
			$checked=0;
			if(isset($selecte[$d->id])){
				$checked=1;
			//		$collection[$d->id]=["name"=>$d->name,"uniq_name"=>$d->uniq_name,"checked"=>$checked];
			}
			$collection[$d->id]=["name"=>$d->name,"uniq_name"=>$d->uniq_name,"checked"=>$checked];
			
		}
        return view('widgets.advert_yandex', [
            'config' => $this->config,"collection"=>$collection
        ]);
    }
}
