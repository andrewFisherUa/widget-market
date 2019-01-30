<?php

namespace App\Videosource;

//use Illuminate\Database\Eloquent\Model;

class UserRef //extends Model
{
    private static $instance=null;
	private $attribs=[];
	private $days=[];

    public static function getInstance(){
	if(self::$instance==null){
	self::$instance=new self;
	}
	return self::$instance;
	}
	public function RegisterData(){
		$date=date("Y-m-d");
		$pdo = \DB::connection("videotest")->getPdo();
		$sql="select date,pid, substring(referrer from '://((?:(?!://).)+?)/') as url, count(*) as cnt from frame_pid 
		where date='$date' group by pid, substring(referrer from '://((?:(?!://).)+?)/'), date order by cnt desc";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql=" insert into frame_refer (
		date,
		pid,
		url,
		cnt)
		select ?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM frame_refer WHERE date=? and pid=? and url=? )
		;";
		$sthInsertPids=$pdo->prepare($sql);
		$sql="update frame_refer 
		set cnt=cnt+?
		WHERE date=? and pid=? and url=?
		";
		$sthUpdatePids=$pdo->prepare($sql);
		foreach ($data as $d){
			$sthUpdatePids->execute([$d['cnt'],$d['date'],$d['pid'],$d['url']]);
			$sthInsertPids->execute([$d['date'],$d['pid'],$d['url'],$d['cnt'],$d['date'],$d['pid'],$d['url']]);
		}
		
		$myday=date("Y-m-d",time()-(3600*168));
		$sql="delete from frame_refer where date <'$myday'";
		$pdo->exec($sql);
		/*$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==9){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete from frame_pid where day <'$myday'";
		$pdo->exec($sql);
		
		print " deleted pids untill $myday !!!!\n";
		}*/
    } 	

	
}
