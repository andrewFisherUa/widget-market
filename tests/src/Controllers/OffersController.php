<?php namespace Mplacegit\Teaser\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Route;
class OffersController extends Controller{
    public function __construct()
    {
    	$this->middleware(['role:admin|manager|super_manager|advertiser']);

    }
	public function index($id,Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		$sql="select 
		      t.id_company,
              t.ind,
              t.title,
              t.src,
              t.img,
			  t.descript,
              t.status
		from teasers_offers_new t
				where t.id_company=$id
				order by t.title
		";
		$collection=\DB::connection("advertise")->select($sql);
		$params=['collection'=>$collection];
		return view('mp-teaser::offers',$params);

    }		
	public function save($id,Request $request){
		$pdo=\DB::connection("advertise")->getPdo();
		$sql="update teasers_offers_new set status=2 where id_company=$id";
		$pdo->exec($sql);
		
		$setstat=$request->input('setstat');
		$stat=3;
		if($setstat){
			$stat=1;
		}
		 $tolds=$request->input('upld-old');
		 $tstolds=$request->input('status-old');
		 $tddesc=$request->input('description');
		
		 $tnames=$request->input('title');
		 $data=[];
		 $tlinks=$request->input('link');
		 if($tstolds){
			 foreach($tstolds as $i=>$g)
			 $data[$i]['status']=$g;
		 }
		 if($tddesc){
			 foreach($tddesc as $i=>$g)
			 $data[$i]['description']=$g;
		 }
		 if($tolds){
			 foreach($tolds as $i=>$g)
			 $data[$i]['old_img']=$g;
			 
		 }
		 
		 if($tnames){
		 foreach($tnames as $i=>$g)
			 $data[$i]['name']=$g;
		}
		
	  	if($tlinks){
		 foreach($tlinks as $i=>$g){
			 
			  $data[$i]['link']=$g;
			  if($g)
			  $data[$i]['ind']=md5($g);
		    }
		}	
		if($request->hasFile('imgInp')){
			
			$d='/home/www/storage.market-place.su/tslist/'.$id.'';
			if(!is_dir($d)){
				mkdir($d);
			}
		 foreach ($request->file('imgInp') as $i=>$g) 
			 $data[$i]['image']=$g;
		 
		}
		$offers=[];
        foreach($data as $k){
			if(((isset($k["old_img"]) 
			&& $k["old_img"]) || (isset($k["image"]) 
			&& $k["image"])) 
			&& isset($k["name"]) 
			&& $k["name"] 
			&& isset($k["ind"]) 
			&& $k["ind"]){
			if(isset($k["image"]) && $k["image"]){	
			$oldfile=$k["image"]->getPathName();
				$newfile='/home/www/storage.market-place.su/tslist/'.$id.'/'.$k["ind"].'.'.$k["image"]->getClientOriginalExtension();
				$newurl='https://storage.market-place.su/tslist/'.$id.'/'.$k["ind"].'.'.$k["image"]->getClientOriginalExtension();
			    if(move_uploaded_file($oldfile,$newfile)){
					$desc=(isset($k['description']))?$k['description']:'';
					$offers[$k["ind"]]=['title'=>$k["name"],
					'src'=>$k["link"],
					'img'=>$newurl,
					'descript'=>$desc,
					'status'=>$stat
					];
				}
			}else{
				$desc=(isset($k['description']))?$k['description']:'';
				$status=(isset($k['status']))?$k['status']:'';
				if($stat==1)
				$status=1;	
				$offers[$k["ind"]]=['title'=>$k["name"],
					'src'=>$k["link"],
					'img'=>$k["old_img"],
					'descript'=>$desc,
					'status'=>$status
					];
			}
			#print "<pre>"; print_r($offers[$k["ind"]]); print "</pre>";
			}
			
		}
		#die();
		$sql="CREATE TEMP TABLE teasers_offers_tmp (
        id_company integer,
        ind character varying(32),
        title character varying(255),
        src character varying(512),
        img character varying(255),
		descript text,
        status smallint DEFAULT 0
        )
		";
		$pdo->exec($sql);

		$sql="insert into teasers_offers_tmp (
              id_company,
              ind,
              title,
              src,
              img,
			  descript,
              status
        )values(?,?,?,?,?,?,?)
		";
		$intt=$pdo->prepare($sql);
        foreach($offers as $ind=>$off){
			  $ret=[
			  $id,
			  $ind,
			  $off["title"],
			  $off["src"],
			  $off["img"],
			  $off["descript"],
			  $off["status"]
			  ];
			  $intt->execute($ret);
			    #print "<pre>";
		        #print_r([$off]);
		        #print "</pre>";
		}		
		
		$sql="
		update teasers_offers_new as a
        set title=b.title,
		      descript=b.descript,
              img=b.img,
              status=case when (a.title = b.title or 1 = $stat) then b.status else 3 end
		FROM(
        select 
		      t.id_company,
              t.ind,
              t.title,
              t.src,
              t.img,
			  t.descript,
              t.status
		from teasers_offers_tmp t
		left join teasers_offers_new n
		on n.id_company=t.id_company 
        and n.ind=t.ind
) as b	  
where a.id_company = b.id_company 
and a.ind = b.ind;
insert into teasers_offers_new(
		 id_company,
              ind,
              title,
              src,
              img,
			  descript,
              status
		)
		select 
		      t.id_company,
              t.ind,
              t.title,
              t.src,
              t.img,
			  t.descript,
              case when 1= $stat then 1 else 3 end
		from teasers_offers_tmp t
		left join teasers_offers_new n
		on n.id_company=t.id_company 
        and n.ind=t.ind
		where n.ind is null  
		";
		$pdo->exec($sql);
				$sql="select 
		      t.id_company,
              t.ind,
              t.title,
              t.src,
              t.img,
              t.status
		from teasers_offers_new t
				where t.id_company=$id and t.status=2
		";
		
		$dv=$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($dv as $d){
			if($d["img"]){
			$f_name=str_replace('https://storage.market-place.su','/home/www/storage.market-place.su',$d["img"]);
			@unlink($f_name);
			}
		}
			$sql="delete 
		        from teasers_offers_new t
				where t.id_company=$id and t.status=2
		";
		$pdo->exec($sql);
		return redirect()->back()->with('message_success', "Данные сохранились.");;
		
		
		

    }		
}	