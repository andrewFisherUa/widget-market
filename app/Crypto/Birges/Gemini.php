<?php

namespace App\Crypto\Birges;

use Illuminate\Database\Eloquent\Model;

class Gemini extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $transactions=[];
	private $name='Gemini';
	public function index($symbols){
		$pairs=['btcusd','ethbtc','ethusd'];
		foreach ($pairs as $pair){
			$curl = curl_init('https://api.gemini.com/v1/pubticker/' . $pair);
			$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			$result=json_decode($json);
			$pair=\App\Crypto\HelpersBirges\Pair::getInstance()->index($symbols, $pair);
			if (!$pair) continue;
			if(!isset($this->transactions[$this->name][$pair['from']])){
				$this->transactions[$this->name][$pair['from']]=[];
			}
			if(!isset($this->transactions[$this->name][$pair['from']][$pair['to']])){
				$this->transactions[$this->name][$pair['from']][$pair['to']]=[];
			}
			$from=mb_strtoupper($pair['from']);
			$to=mb_strtoupper($pair['to']);
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_from']=$result->volume->$to/$result->volume->$from;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['volume']=$result->volume->$from;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_to']=1/($result->volume->$to/$result->volume->$from);
		}
		
		foreach ($this->transactions as $name=>$transactions){
			foreach ($transactions as $from=>$transaction){
				foreach ($transaction as $to=>$detail){
					if ($to=='usd'){
						$this->transactions[$name][$from][$to]['price_from_to_usd']=$detail['price_from'];
						$this->transactions[$name][$from][$to]['volume_to_usd']=$detail['volume']*$detail['price_from'];
						$this->transactions[$name][$from][$to]['price_to_to_usd']=$detail['price_to']*$detail['price_from'];
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
