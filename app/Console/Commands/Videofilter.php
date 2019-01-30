<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Videofilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videofilter';

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
$file="/etc/nginx/blockips.conf";	
$arfa=file($file);
$ips=[];
foreach($arfa as $f){
$tm=explode(" ",$f);
if(count($tm)>1){
$ip=trim(preg_replace('/[^0-9\.]/','',$tm[1]));
}else{
continue;
}
$ips[$ip]=1;

}

	   $pdo = \DB::connection("pgstatistic")->getPdo();
        $data=date("Y-m-d");
		$sql="create temp table tmp(ip varchar(15),event varchar(127),cnt int);
		insert into tmp( ip,event,cnt)
select ip,event,sum(cnt) as cnt
from videostatistic_ips where day ='".$data."'
group by ip,event;
		";
		$pdo->exec($sql);
		$sql="

select t.ip
,coalesce(t_r.cnt,0) as srcRequest
,coalesce(t_p.cnt,0) as filterPlayMedia
from (
select ip from tmp group by ip)
t
left join (
select ip,sum(cnt) as cnt from tmp where event ='srcRequest' group by ip
) t_r
on t_r.ip=t.ip
left join (
select ip,sum(cnt) as cnt from tmp where event ='filterPlayMedia' group by ip
) t_p
on t_p.ip=t.ip
where coalesce(t_p.cnt,0) =0 and coalesce(t_r.cnt,0) >150
order by 2 desc
";
$result = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
$text="";
foreach($result as $r){

$ips[$r["ip"]]=1;
}
foreach($ips as $key=>$o)
{
$text.="deny ".$key.";\n";
}



#$text.="deny 79.122.189.122;\n";

file_put_contents($file,$text);
print $text;
`service nginx restart`;

    }
}
