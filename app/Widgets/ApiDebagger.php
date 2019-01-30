<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class ApiDebagger extends AbstractWidget
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
		$maska=null;
			#print "<pre>"; var_dump($this->config); print "</pre>";
		if(!isset($this->config["page"]))
			$this->config["page"]=[];
		if(isset($this->config["maska"])){
			$vmp=$this->config["maska"]["categories"];
			
			$this->config["maska"]["categories"]=[];
			if($vmp){
				$sql="select id,uniq_name from yandex_categories where id in($vmp) order by uniq_name";
				$csz=\DB::connection("cluck")->select($sql);
				foreach($csz as $cs){
				$this->config["maska"]["categories"][$cs->id]=$cs->uniq_name;	
				}
				//var_dump($csz);
			}
			#var_dump($this->config["maska"]); die();
			#$k=explode(",",$this->config["maska"]["categories"]);
			#var_dump($k);
			#die();
		
		}
		if(isset($this->config["page"]["id_category"]) && $this->config["page"]["id_category"]){
			
			$sql="select id,uniq_name from yandex_categories where id in(".$this->config["page"]["id_category"].")";
			$csz=\DB::connection("cluck")->select($sql);
			foreach($csz as $cs){
				$this->config["page"]["categories"][$cs->id]=$cs->uniq_name;	
				}
			#$category=\DB::connection("cluck")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
			#if($category)
			//$this->config["page"]["category"]=$category;
		}
	

        return view('widgets.api_debagger', [
            'config' => $this->config,
        ]);
    }
}
