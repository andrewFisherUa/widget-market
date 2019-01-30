<?php

namespace App\Crypto\Birges;

use Illuminate\Database\Eloquent\Model;

class Bitflyer extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $transactions=[];
	private $name='Bitflyer';
	public function index($symbols){
		$curl = curl_init('https://api.bitflyer.com/v1/getticker');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		var_dump($json);
		exit;
		curl_close($curl);
		$result=json_decode($json);
		var_dump($result);
		exit;
		if (isset($results->error)){
			return;
		}
		foreach ($results as $result){
			$pair=\App\Crypto\HelpersBirges\Pair::getInstance()->index($symbols, $result->pair);
			if (!$pair) continue;
			if(!isset($this->transactions[$this->name][$pair['from']])){
				$this->transactions[$this->name][$pair['from']]=[];
			}
			if(!isset($this->transactions[$this->name][$pair['from']][$pair['to']])){
				$this->transactions[$this->name][$pair['from']][$pair['to']]=[];
			}
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_from']=$result->mid;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['volume']=$result->volume;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_to']=1/$result->mid;
		}
		foreach ($this->transactions as $name=>$transactions){
			foreach ($transactions as $from=>$transaction){
				foreach ($transaction as $to=>$detail){
					if ($to=='usd'){
						$this->transactions[$name][$from][$to]['price_from_to_usd']=$detail['price_from'];
						$this->transactions[$name][$from][$to]['volume_to_usd']=$detail['volume']*$detail['price_from'];
						$this->transactions[$name][$from][$to]['price_to_to_usd']=$detail['price_to']*$detail['price_from'];
					}
					elseif ($to=='eur'){
						$convert=\App\Crypto\HelpersBirges\Pair::getInstance()->convert($to, 'usd');
						$this->transactions[$name][$from][$to]['price_from_to_usd']=$detail['price_from']*$convert;
						$this->transactions[$name][$from][$to]['volume_to_usd']=$detail['volume']*$detail['price_from']*$convert;
						$this->transactions[$name][$from][$to]['price_to_to_usd']=$detail['price_to']*$detail['price_from']*$convert;
					}
					else{
						if (isset($this->transactions[$name][$to]['usd']) and isset($this->transactions[$name][$from]['usd'])){
							$this->transactions[$name][$from][$to]['price_from_to_usd']=$detail['price_from']*$this->transactions[$name][$to]['usd']['price_from'];
							$this->transactions[$name][$from][$to]['volume_to_usd']=$detail['volume']*$detail['price_from']*$this->transactions[$name][$to]['usd']['price_from'];
							$this->transactions[$name][$from][$to]['price_to_to_usd']=$detail['price_to']*$this->transactions[$name][$from]['usd']['price_from'];
						}
						else{
							$this->transactions[$name][$from][$to]['price_from_to_usd']=0;
							$this->transactions[$name][$from][$to]['volume_to_usd']=0;
							$this->transactions[$name][$from][$to]['price_to_to_usd']=0;
						}
					}
					$convert=\App\Crypto\HelpersBirges\Pair::getInstance()->convert('usd', 'rub');
					$this->transactions[$name][$from][$to]['price_from_to_rub']=$this->transactions[$name][$from][$to]['price_from_to_usd']*$convert;
					$this->transactions[$name][$from][$to]['volume_to_rub']=$this->transactions[$name][$from][$to]['volume_to_usd']*$convert;
					$this->transactions[$name][$from][$to]['price_to_to_rub']=$this->transactions[$name][$from][$to]['price_to_to_usd']*$convert;
				}
			}
		}
		return $this->transactions;
	}
}
