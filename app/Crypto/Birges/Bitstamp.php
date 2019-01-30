<?php

namespace App\Crypto\Birges;

use Illuminate\Database\Eloquent\Model;

class Bitstamp extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $transactions=[];
	private $name='Bitstamp';
	public function index($symbols){
		//eurusd - не зайдейстована пара
		$pairs=['btcusd','btceur','xrpusd','xrpeur','xrpbtc','ltcusd','ltceur','ltcbtc','ethusd','etheur','ethbtc','bchusd','bcheur','bchbtc'];
		foreach ($pairs as $pair){
			$curl = curl_init('https://www.bitstamp.net/api/v2/ticker/btcusd/');
			$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
				//CURLOPT_PROXY => "103.15.187.52:3128",
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			var_dump($json);
			exit;
			curl_close($curl);
			$results=json_decode($json);
			var_dump($results);
			exit;
			/*
			if ($results->status!='0000'){
				return;
			}
			*/
		}
		
		foreach ($results->data as $symb=>$result){
			$symb.="KRW";
			$pair=\App\Crypto\HelpersBirges\Pair::getInstance()->index($symbols, $symb);
			if (!$pair) continue;
			if(!isset($this->transactions[$this->name][$pair['from']])){
				$this->transactions[$this->name][$pair['from']]=[];
			}
			if(!isset($this->transactions[$this->name][$pair['from']][$pair['to']])){
				$this->transactions[$this->name][$pair['from']][$pair['to']]=[];
			}
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_from']=$result->average_price;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['volume']=$result->volume_1day;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_to']=1/$result->average_price;
		}
		foreach ($this->transactions as $name=>$transactions){
			foreach ($transactions as $from=>$transaction){
				foreach ($transaction as $to=>$detail){
					$convert=\App\Crypto\HelpersBirges\Pair::getInstance()->convert($to, 'usd');
					$this->transactions[$name][$from][$to]['price_from_to_usd']=$detail['price_from']*$convert;
					$this->transactions[$name][$from][$to]['volume_to_usd']=$detail['volume']*$detail['price_from']*$convert;
					$this->transactions[$name][$from][$to]['price_to_to_usd']=$detail['price_to']*$detail['price_from']*$convert;
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
