<?php

namespace App\MPW\Statistic\Video;

use Illuminate\Database\Eloquent\Model;

class StatisticBlocks extends Model
{	
	
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance===null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public $group=[];
	
	public function Group($data, $datetime){
		$date=date("Y-m-d", strtotime($datetime));
		
		if (!isset($this->group[$data["page_key"]]))
		$this->group[$data["page_key"]]=[];
		if (!isset($this->group[$data["page_key"]][$date]))
		$this->group[$data["page_key"]][$date]=[];
		if (!isset($this->group[$data["page_key"]][$date][$data['block']]))
		$this->group[$data["page_key"]][$date][$data['block']]=[];
	}
	
	public function insert($data){
		$pdo = \DB::connection('videostatistic')->getPdo();
		$sql="insert into statistic_blocks (page_key,date,id_block)
			select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM statistic_blocks WHERE page_key=? and date=? and id_block=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update statistic_blocks set id_block=?
		  WHERE page_key=? and date=? and id_block=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $page_key=>$dates){
			foreach ($dates as $date=>$blocks){
				foreach ($blocks as $block=>$events){
					$sthUpdate->execute([$block,$page_key,$date,$block]);
					$sthInsert->execute([$page_key,$date,$block,$page_key,$date,$block]);
				}
			}
		}
	}
	
	public function isertStat($date){
		$pdo = \DB::connection('videostatistic')->getPdo();
		$sql="select date, id_block, count(page_key) as requested from statistic_blocks where date='$date' group by date, id_block";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$pdo = \DB::connection()->getPdo();
		$sql="insert into video_statistic_blocks_new_player (date,id_block,requested) select ?,?,? WHERE NOT EXISTS (SELECT 1 FROM 
		video_statistic_blocks_new_player WHERE date=? and id_block=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update video_statistic_blocks_new_player set requested=? WHERE date=? and id_block=?";
		$sthUpdate=$pdo->prepare($sql);
		foreach ($data as $pad){
			$sthUpdate->execute([$pad['requested'],$pad['date'],$pad['id_block']]);
			$sthInsert->execute([$pad['date'],$pad['id_block'],$pad['requested'],$pad['date'],$pad['id_block']]);
		}
	}
	
}
