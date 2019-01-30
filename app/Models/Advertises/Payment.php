<?php

namespace App\Models\Advertises;

//use Illuminate\Database\Eloquent\Model;

class Payment //extends Model
{
	 protected $connection= 'advertise';
     #protected $table = 'payment_history';
	 public function insertSumma($user_id,$author_id,$summa,$descr='пополнение в личном кабинете',$tt='popolnenie'){
		$myuser = \App\User::find($user_id);
		 
     if($myuser && $myuser->hasRole("advertiser")){
	 }else{
		 return false;
	 }
	 if($author_id)	{
		 $author=\App\User::find($author_id);
		 if($author)
		 $author_name=$author->name;
	     else
	     $author_name='неопределен';
	 }else{
		 $author_name='система';
	 }
	 
		$pdo=\DB::connection("advertise")->getPdo();
		#return;
		$sql="insert into payment_history (
    type,
    user_id,
    summa,
    description,
    author_id,
    author_name
)
values (
'$tt',
$user_id,
$summa,
'$descr',
$author_id,
'$author_name'
)";
$pdo->exec($sql);
return $this->correctBalance($user_id);
	 }
  public function correctBalance($user_id,$flag=0){
	  $pdo=\DB::connection("advertise")->getPdo();
	  $myuser = \App\User::find($user_id);
	  if($myuser && $myuser->hasRole("advertiser")){
	 }else{
		 return false;
	 }
	 $sql=" insert into user_profile(
	 user_id,
	 name
	 ) select $user_id,'".$myuser->name."' 
	 WHERE NOT EXISTS (SELECT 1 FROM  user_profile WHERE user_id=$user_id)
	 "; 
	 $pdo->exec($sql);
	 #if(!$flag) return;
	 
	 $sql="select sum(summa) as summa from payment_history
	 where user_id=$user_id
	 ";
	 $sumd=\DB::connection("advertise")->select($sql);

	 $summa1=0; #расходы
	 $summa2=0; #общий баланс
	 if($sumd){
		 $summa1=$sumd[0]->summa;
	 }
	 $hupp=\App\UserProfile::where(["user_id"=>$user_id])->first();
	 if($hupp){
	 $hupp->balance=$summa1;
	 #$hupp->balance=0;
	 $hupp->save();
	 }
	 
	 /*
	 if($hupp){
		 
	 $summa2=$hupp->balance;
	 }
	 */
	 $sql="update
	 user_profile
	 set balance=$summa1
	 where user_id=$user_id
	 ";
	  $pdo->exec($sql);
if($summa1>0){
	
	
		  $sql="update advertise_before_search set status = 1
		   WHERE status = 6 and advertise_id in(
		   select id from advertises 
		   where user_id=$user_id and status =6
		   )
		   ";
	      \DB::connection("advertise")->statement($sql);
		  $dbdu=[];
		 /*
		  $sql="select id from advertises where user_id=$user_id and status =6";
	      $ds=\DB::connection("advertise")->select($sql);
		  foreach($ds as $d){
			 $dbdu[]=$d->id;
		  }
		  */
		  #if($dbdu){
			  
			$sql="
			insert into advertise_before_search (advertise_id,status) 
		    select t.id,1 from advertises t
                    left join advertise_before_search t1
                    on t1.advertise_id = t.id 
                    where t.user_id=$user_id and t.status=6 and t1.advertise_id is null
			" ; 
			\DB::connection("advertise")->statement($sql);
			
		  #}
		  
		  
		$sql="update advertises set status =1 
		where user_id=$user_id and status =6
		";  
		   $pdo->exec($sql);
	  }
	  #echo " $summa1 / $summa2 \n";
      return 23;
	 #die();
	 
  }	
}
