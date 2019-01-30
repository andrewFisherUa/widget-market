<?php

namespace App\Payments;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $table = "user_invoices";
	public function Requisite(){
		return $this->belongsTo('\App\Requisite', 'requisite_id');
	}
    public static function createOrCheck($requisite,$summa,$type){
	$pdo=\DB::connection()->getPdo();
	$nds=0;
    $total=$summa;	
	if($requisite->type_payout==2){
		$nds=$summa*18/100;
		$total=$summa+$nds;
	}
	$nowYear=date('Y');
	$sql="
	insert into user_invoices(
	year,
	user_id,
	requisite_id,
	summa,
	type,
	nds,
	total,
	payment_date,
	number
	)
	select $nowYear
	,".$requisite->user_id."
	,".$requisite->id."
	,".$summa."
	,".$type."
	,".$nds."
	,".$total." ";
	if($type==2){
		$sql.=",(NOW() + interval '3' day)::date ";
	}else{
		$sql.=",(NOW() + interval '14' day)::date  ";
	}
	$sql.=",coalesce(max(number),0)+1 from user_invoices where year=$nowYear and user_id =".$requisite->user_id."
	returning id
	";
	$data=$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
	if(!$data || !$data["id"]) abort(500);
	return $data["id"]; 
	}
	public function num2str($num) {
	$nul='ноль';
	$ten=array(
		array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
		array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
	);
	$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
	$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
	$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
	$unit=array( // Units
		array('копейка' ,'копейки' ,'копеек',	 1),
		array('рубль'   ,'рубля'   ,'рублей'    ,0),
		array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
		array('миллион' ,'миллиона','миллионов' ,0),
		array('миллиард','милиарда','миллиардов',0),
	);
	//
	list($rub) = explode('.',sprintf("%015.2f", floatval($num)));
	$kop = round(($num-$rub)*100);
	$out = array();
	if (intval($rub)>0) {
		foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
			if (!intval($v)) continue;
			$uk = sizeof($unit)-$uk-1; // unit key
			$gender = $unit[$uk][3];
			list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
			// mega-logic
			$out[] = $hundred[$i1]; # 1xx-9xx
			if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
			else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
			// units without rub & kop
			if ($uk>1) $out[]= $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
		} //foreach
	}
	else $out[] = $nul;
	$out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
	$out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
	$text = trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
   }
   function morph($n, $f1, $f2, $f5) {
	$n = abs(intval($n)) % 100;
	if ($n>10 && $n<20) return $f5;
	$n = $n % 10;
	if ($n>1 && $n<5) return $f2;
	if ($n==1) return $f1;
	return $f5;
   }
   public function summaToStr(){
		  return $this->num2str($this->total);
	
   }

}
