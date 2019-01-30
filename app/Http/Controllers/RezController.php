<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use Charts;
class RezController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function Rez(){
	\Auth::user()->touch();
	
	$pdo = \DB::connection('videotest')->getPdo();
	$sql="
select 
t.datetime
,l.title
,t.id_src
,t.cnt 
,t.errors
,t.success
,coalesce(t_ru.cnt,0) as ru_start
,coalesce(t_cis.cnt,0) as cis_start
,coalesce(t_ru_error.cnt,0) as ru_error
,coalesce(t_cis_error.cnt,0) as cis_error
,coalesce(t_ru_success.cnt,0) as ru_success
,coalesce(t_cis_success.cnt,0) as cis_success
,coalesce(t_desc.cnt,0) as desc_start
,coalesce(t_mob.cnt,0) as mob_start
,coalesce(t_desc_error.cnt,0) as desc_error
,coalesce(t_mob_error.cnt,0) as mob_error
,coalesce(t_desc_success.cnt,0) as desc_success
,coalesce(t_mob_success.cnt,0) as mob_success
from (
select t.datetime,t.id_src,t.cnt 
,coalesce(te.cnt,0) as errors
,coalesce(ts.cnt,0) as success
from (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_start'
group by datetime,id_src
) t
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_error'
group by datetime,id_src
) te
on te.id_src=t.id_src and te.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_success'
group by datetime,id_src
) ts
on ts.id_src=t.id_src and ts.datetime=t.datetime
) t
inner join links l on l.id=t.id_src
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_start' and country='RU'
group by datetime,id_src
) t_ru on t_ru.id_src=t.id_src and t_ru.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_start' and country='CIS'
group by datetime,id_src
) t_cis on  t_cis.id_src=t.id_src and  t_cis.datetime=t.datetime


left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_error' and country='RU'
group by datetime,id_src
) t_ru_error on t_ru_error.id_src=t.id_src and t_ru_error.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_error' and country='CIS'
group by datetime,id_src
) t_cis_error on  t_cis_error.id_src=t.id_src and  t_cis_error.datetime=t.datetime


left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_success' and country='RU'
group by datetime,id_src
) t_ru_success on t_ru_success.id_src=t.id_src and t_ru_success.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_success' and country='CIS'
group by datetime,id_src
) t_cis_success on  t_cis_success.id_src=t.id_src and  t_cis_success.datetime=t.datetime

left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_start' and  device=0
group by datetime,id_src
) t_desc on  t_desc.id_src=t.id_src and  t_desc.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_start' and  device=1
group by datetime,id_src
) t_mob on  t_mob.id_src=t.id_src and  t_mob.datetime=t.datetime

left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_error' and  device=0
group by datetime,id_src
) t_desc_error on  t_desc_error.id_src=t.id_src and  t_desc_error.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_error' and  device=1
group by datetime,id_src
) t_mob_error on  t_mob_error.id_src=t.id_src and  t_mob_error.datetime=t.datetime

left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_success' and  device=0
group by datetime,id_src
) t_desc_success on  t_desc_success.id_src=t.id_src and  t_desc_success.datetime=t.datetime
left join (
select datetime,id_src,count(*) as cnt
from src_events where event='loading_success' and  device=1
group by datetime,id_src
) t_mob_success on  t_mob_success.id_src=t.id_src and  t_mob_success.datetime=t.datetime
	";
	 $values = $pdo->query($sql, \PDO::FETCH_ASSOC)->fetchALL(\PDO::FETCH_CLASS);
		return view('statistic.video.rez',["values"=>$values]);
	}
}
