<?php

namespace App\Crypto\Birges;

use Illuminate\Database\Eloquent\Model;

class Gdax extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	private $transactions=[];
	private $name='Gdax';
	public function index($symbols){
		$curl = curl_init('https://api-public.sandbox.gdax.com/products/stats');
		$options = array(
			CURLOPT_HTTPGET => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 40,
			CURLOPT_HTTPHEADER => array(
					'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
					'Accept-language:ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
					'User-Agent:Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36'
				),
		);
		curl_setopt_array($curl, $options);
		$json=(curl_exec ($curl));
		curl_close($curl);
		$results=json_decode($json);
		
		var_dump($results);
		exit;
		foreach ($results->data as $symb=>$result){
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
