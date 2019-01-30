<?php
namespace App\Console\Commands\Next;
use Illuminate\Support\Facades\Redis;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
class Vendor extends Command
{
	public $rr1=0;
	public $rr2=0;
	public $rr3=0;
		use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'next:vendor';

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
    public function checkMark($t5)
    {
		if(!$t5) return false;
		if(preg_match('/^[a-z]{2,}$/ui',$t5)){
			$this->rr1++;
			return true;
		}
		if(preg_match('/^[a-z]{2,}[\&\-\s]+[a-z]{2,}$/ui',$t5)){
			 $this->rr2++;
			 return true;
			 #echo $t5."\n";	 sleep(1);
		}
		if(preg_match('/^[а-я]{2,}$/ui',$t5)){ 
			 $this->rr3++;

			# sleep(1);
			 return true;
			 echo $t5."\n";	 
		}

		return false;
	}		
    public function get_offer($url){
#       print $url."\n"; 
            $ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
#curl_setopt($ch, CURLOPT_HEADER, 0); // читать заголовок
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

#curl_setopt($ch, CURLOPT_HEADER, 1); // читать заголовок
#curl_setopt($ch, CURLOPT_NOBODY, 1); // читать ТОЛЬКО заголовок без тела
$result = curl_exec($ch);  
$v = curl_getinfo ( $ch);

curl_close($ch);
if($v["http_code"] !="200"){
print $url."\n"; 
print $v["http_code"]."\n";
echo "битая страница\n";

}
#print $v["http_code"]."\n";
#print $v["size_download"]."\n";


#var_dump($v);




    }
    public function get_catalog($url){
             $ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, 0); // читать заголовок

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

$result = curl_exec($ch);  
curl_close($ch);
$simplexml_object = simplexml_load_string($result);
$res = $simplexml_object->xpath('/yml_catalog/shop/offers/offer/url');

while(list( , $node) = each($res)) {
    $text = trim((string)$node); 
     $this->get_offer($text);

#sleep(1);
#    echo '/a/b/c: ',$node,"\n";
}


#var_dump($res);


    }
    public function ost1(){
    $url='http://automediya.ru/yandexmark/next.php';






    print $url.""; 
               $this->get_catalog($url);




    }

    public function handle()
    {
$this->ost1();
     return ;

		Redis::command('DEL', ['_vendor_']);
		
		
		#Redis::command('HGETALL', ['_vendor_']);
		$arr=Redis::command('HGETALL', ['_vendor_']);
		foreach($arr as $k=>$a){
		var_dump($k);	
		}
		
		
		
		$postpdo = \DB::connection("product_next")->getPdo();
		$sql="select * from vendors order by name";
		 $data=$postpdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		 $i=0;
		 foreach($data as $d){
			 $t5=trim($d["ind"]);
			 if($this->checkMark($t5)){
				 //Redis
			 Redis::command('hset', ['_vendor_',$t5,$d["id"]]);
			 #if($i>5) break;
			 #echo $t5."\n";	 
			 $i++;
			 }
			
		 }
        print $this->rr1."-". $this->rr2."-". $this->rr3."\n";
    }
}
