<?php

namespace App\Crypto\Birges;

use Illuminate\Database\Eloquent\Model;

class Btcbox extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $transactions=[];
	private $name='Btcbox';
	public function index($symbols){
		//eurusd - не зайдейстована пара
		$pairs=['btc','ltc','eth','bch'];
		foreach ($pairs as $pair){
			$curl = curl_init('https://www.btcbox.co.jp/api/v1/ticker/' . $pair . '/');
			$options = array(
				CURLOPT_HTTPGET => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT => 40,
			);
			curl_setopt_array($curl, $options);
			$json=(curl_exec ($curl));
			curl_close($curl);
			$result=json_decode($json);
			$symb="" . $pair . "jpy";
			$pair=\App\Crypto\HelpersBirges\Pair::getInstance()->index($symbols, $symb);
			if (!$pair) continue;
			if(!isset($this->transactions[$this->name][$pair['from']])){
				$this->transactions[$this->name][$pair['from']]=[];
			}
			if(!isset($this->transactions[$this->name][$pair['from']][$pair['to']])){
				$this->transactions[$this->name][$pair['from']][$pair['to']]=[];
			}
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_from']=($result->high+$result->low)/2;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['volume']=$result->vol;
			$this->transactions[$this->name][$pair['from']][$pair['to']]['price_to']=1/(($result->high+$result->low)/2);
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
