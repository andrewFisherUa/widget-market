<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class LogController extends Controller
{
    public function AuthLog(){
		\Auth::user()->touch();
		$pdo = \DB::connection()->getPdo();
		
		//$sql="select array_to_string(array_agg(distinct t1.user_id),', ') as user_id, array_to_string(array_agg(distinct t1.ip_adress),', ') as ip_adress from auth_logs t1 join (select * from auth_logs) t2 on t1.user_id=t2.user_id and t1.ip_adress<>t2.ip_adress or t1.user_id<>t2.user_id and t1.ip_adress=t2.ip_adress";
		/*$sql="create temp table auth_all as select t1.user_id, t1.ip_adress from auth_logs t1 group by t1.user_id, t1.ip_adress order by t1.user_id";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="select array_to_string(array_agg(distinct user_id),', ') as user_id, ip_adress from auth_all group by ip_adress having (count(user_id)>1)";
		$groups=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="select * from auth_logs where (user_id, created_at) in (select user_id,max(created_at) from auth_logs group by user_id)";
		$last_logins=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);*/
		
		$sql="create temp table auth_all as select array_to_string(array_agg(distinct user_id),', ') as user_id, ip_adress from auth_logs group by ip_adress having (count(distinct user_id)>1);";
		$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$sql="select user_id, array_agg(distinct ip_adress) as ip_adress from auth_all group by user_id;";
		$data=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		$groups=array();
		foreach($data as $group){
            
            $groups[]=explode(',',$group['user_id']);
        }
        foreach($groups as $k =>$group){
            foreach($groups as $kk=>$s_group){

                if(!empty(array_intersect($group,$s_group))){

                    $groups[$k]= array_unique( array_merge($groups[$k],$s_group));
                    $groups[$kk]= array_unique( array_merge($groups[$k],$s_group));


                }
            }
        }
        $res=array();
        foreach($groups as $k =>$group){
            $res[]=join(',',$group);


        }
        $result=array_unique($res);
		$sql="select * from auth_logs where (user_id, created_at) in (select user_id,max(created_at) from auth_logs group by user_id)";
		$last_logins=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		return view('admin.auth_logs.index', ['result'=>$result, 'last_logins'=>$last_logins]);
	}
	
	public function AuthLogDetail($ids, Request $request){
		\Auth::user()->touch();
		$ip=$request->ip;
		$pdo = \DB::connection()->getPdo();
		$sql="select ip_adress, user_id, date, created_at from auth_logs where user_id in ($ids) group by ip_adress,user_id,date,created_at order by ip_adress, date asc";
		$groups=$pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_ASSOC);
		return view('admin.auth_logs.detail', ['groups'=>$groups, 'ip'=>$ip]);
	}
}
