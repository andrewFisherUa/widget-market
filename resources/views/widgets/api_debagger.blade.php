@if(isset($config["ids"]))	



	@if(1==0)
		<div class="row panel panel-primary">
            Настройки площадки
	    </div>	
        @endif		
		<div class="row panel panel-primary">
	        Настройки ваиджета @if(isset($config["wid"])) <a href="/widget/editor/{{$config["wid"]}}" target="_blank">№ {{$config["wid"]}}</a>@endif
	    </div>		
		@if(isset($config["maska"]))
		<div class="row panel panel-primary" style ="font-size:14px">
	        <div>Маска: @if($config["maska"]["status"]) включены <span id="removemask_ptn" class="glyphicon glyphicon-remove-sign" title="отключить"></span> @else отключены <span id="includevemask_ptn" class="glyphicon glyphicon-ok-sign" title="включить">  @endif</div>
			<table border ="1" style="width:100%;font-size:14px">
			<tr>
			<td>
			url
			</td>
			<td>
			{{$config["maska"]["url"]}}
			</td>			
			</tr>
			<tr>
			<td>
			текст
			</td>
			<td>
			@php 
			$config["maska"]["searchtext"]=preg_replace('/[\&\|\)\(]+/',' ',$config["maska"]["searchtext"]);
			@endphp
			{{$config["maska"]["searchtext"]}}
			</td>			
			</tr>
			@if($config["maska"]["categories"])
			<tr>
			<td>
            Категории			
			</td>
			<td>
			@foreach($config["maska"]["categories"] as $id=>$category)
			<div style ="border-bottom:1px solid #000;">
			{{$category}}
			</div>
			@endforeach
			</td>			
			</tr>				
			@endif	
			</table>
	    </div>
		@endif
		<div class="row panel panel-primary" style ="font-size:14px">
         <div>Настройки страницы: 
			@if($config["page"]) @if($config["page"]["status"]) включены <span id="removepage_ptn" class="glyphicon glyphicon-remove-sign" title="отключить"></span> @else отключены <span id="includepage_ptn" class="glyphicon glyphicon-ok-sign" title="включить">  @endif @else отсутствует	@endif
        @php 
		$searchtext="";
		if($config["page"]){
			$searchtext=$config["page"]["searchtext"];
		}
		
        @endphp		
		</div>			
		<table border ="1" id="mypage_attributes" style="width:100%;font-size:14px">
			<tr>
			<td>
			Фраза
			</td>
			<td>
			<input type="text" id="page_search" value="{{$searchtext}}">
			</td>			
			</tr>
			<tr>
            <tr>
			<td>
			Категория
			</td>
			<td>
			<ol id="choicen_choice">
			@if(isset($config["page"]["categories"]) && $config["page"]["categories"])
				@foreach($config["page"]["categories"] as $id=>$category)
			<li onclick="this.parentNode.removeChild(this);"><div class="btn btn-default btn-sm posit-rel"><input name="cat_yy" value="{{$id}}" type="hidden">{{$category}}</div></li>
			    @endforeach
			@endif	
			@if(isset($config["page"]["category"]) && $config["page"]["category"])
			<li onclick="this.parentNode.removeChild(this);"><div class="btn btn-default btn-sm posit-rel"><input name="cat_yy" value="{{$config["page"]["category"]["id"]}}" type="hidden">{{$config["page"]["category"]["uniq_name"]}}</div></li>
			@endif
			</ol>
			<div data-id="0" class="glyphicon glyphicon-edit" onclick="createMyTreeElenent(this,'search','Категории поиска');" >выбрать категорию</div>
			</td>			
			</tr>
			<tr>			
			
			
			
			<td colspan="2" style="text-align:center;padding:4px">
		   <button type="button" id="mypage_submit" class="btn btn-primary btn-sm">Сохранить изменения</button>
			</td>			
			</tr>
		 </table>	
		
		
		 
		 
		
	    </div>
		<div class="row panel panel-primary" style ="font-size:14px"> 
		 <a href="/adv_/sinonim" class="btn btn-success" target="_blank" style="width:100%">База синонимов</a>
		</div>

    <div class="modal fade" id="myModalCategory" role="dialog">
    <div class="modal-dialog">
       <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Выбрать категорию</h4> 
        </div>

        <div class="modal-body">
		<div class="row">
		<div class="col-lg-9">  
		<input type="text" class = "microsearch_input">
		</div><div class="col-lg-3">
		<button type="button" class = "microsearch_button" class="btn btn-primary btn-sm">Поиск</button>
		</div>
		</div>
        <div class="row" id ="teletree">
		</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Сохранить</button>
        </div>
      </div>
     </div>  
    </div>		
	
<input type="hidden" id="maskpage_idserver" value="{{$config['ids']}}">	
<input type="hidden" id="maskpage_md5" value="{{$config['md5']}}">	
@push('cabinet_home')
<link rel="stylesheet" type="text/css" href="https://widget.market-place.su/projects/styles/tree.css?v=1.0"/>
<style>
#choicen_choice{
list-style:none;
padding:0;
}
</style>
@endpush
@push('cabinet_home_js')
<script src="https://widget.market-place.su/projects/mytree.js?v=1.0"></script>
<script>
    var KK=null;
 function createMyTreeElenent(obj,cls,title,level){
  KK=obj;
  var resContainer=document.querySelector("#mypage_attributes #choicen_choice");	 
 
  var prevs=[];
  if(resContainer){
   var lis = resContainer.querySelector("input[name='cat_yy']");
   if(lis){
	   prevs.push(lis.value);
   }
  }
   $("#myModalCategory h4").html(title); 
    var tmp = new window.ContextMyCategory(obj,cls,$("#myModalCategory").get(0),resContainer,prevs,function(data){
	  
    var resContainer=document.querySelector("#mypage_attributes #choicen_choice");	  
	
			
while (resContainer.firstChild) {
    resContainer.removeChild(resContainer.firstChild);
}
/*
	var lss=resContainer.querySelectorAll("li");
	alert(resContainer.innerHTML);
	for (var i=0,j=lss.length;i<j;i++){
		if(lss[i]){
		lss[i].parentNode.removeChild(lss[i]);
		j--;
		}
	}
	*/
	//return;
	  var lastInd=0;
	  var lastName="";
	  for(ind in data){
		  lastInd=ind;
		  lastName=data[ind];
		 var lsn=document.createElement("li");
	var d=document.createElement("DIV");
    d.className='btn btn-default btn-sm posit-rel';
	var inp=document.createElement("input");
    inp.type="hidden";
    inp.name="cat_yy";
	inp.value=lastInd;
    d.appendChild(inp);
    d.appendChild(document.createTextNode(lastName));
	lsn.appendChild(d);
	lsn.onclick=function(){
	 this.parentNode.removeChild(this); 
	}
	resContainer.appendChild(lsn); 
		  
		  
		  
		
	  }
	  /*
	if(!lastInd) return;;
	var lsn=document.createElement("li");
	var d=document.createElement("DIV");
    d.className='btn btn-default btn-sm posit-rel';
	var inp=document.createElement("input");
    inp.type="hidden";
    inp.name="cat_yy";
	inp.value=lastInd;
    d.appendChild(inp);
    d.appendChild(document.createTextNode(lastName));
	lsn.appendChild(d);
	lsn.onclick=function(){
	 this.parentNode.removeChild(this); 
	}
	resContainer.appendChild(lsn);
	*/
	},function(data){
      return false;
  },level);;	
   $("#myModalCategory").modal('toggle');
} 
$( document ).ready(function() { 

  var idserver=$('#maskpage_idserver').val();
  var md5=$('#maskpage_md5').val();
  

  
  
  
  $('#removemask_ptn').click(function(){
	       var data={_token: $('meta[name=csrf-token]').attr('content')
		   ,idserver:idserver
		   ,flag:0
		   ,md5: md5};
		   //console.log(["data",data]); return;
	  	    $.post('/serverpage_setting_maska',data, function(response) {
				        if(response.hasOwnProperty('ok')){
							location.reload();
						}
						//console.log(response);
			});
  });
  $('#includevemask_ptn').click(function(){
	       var data={_token: $('meta[name=csrf-token]').attr('content')
		   ,idserver:idserver
		   ,flag:1
		   ,md5: md5};
	  	    $.post('/serverpage_setting_maska',data, function(response) {
				        if(response.hasOwnProperty('ok')){
							location.reload();
						}
			});
  }); 
  $('#removepage_ptn').click(function(){
	       var data={_token: $('meta[name=csrf-token]').attr('content')
		   ,idserver:idserver
		   ,flag:0
		   ,md5: md5};
		   // console.log(data);
		   //return;
		   //console.log(["data",data]); return;
	  	    $.post('/serverpage_setting_plge_flag',data, function(response) {
				        if(response.hasOwnProperty('ok')){
							location.reload();
						}
						//console.log(response);
			});
  });
  $('#includepage_ptn').click(function(){
	       var data={_token: $('meta[name=csrf-token]').attr('content')
		   ,idserver:idserver
		   ,flag:1
		   ,md5: md5};
		   
		   //return;
	  	    $.post('/serverpage_setting_plge_flag',data, function(response) {
				        if(response.hasOwnProperty('ok')){
							location.reload();
						}
			});
  });   
  $('#mypage_submit').click(function(){
	 var text=$('#mypage_attributes input#page_search').val();
	 var id_category=0;
	 var k3=[];
	  $("#mypage_attributes #choicen_choice input[name='cat_yy']").each(function(){
		  
		  id_category=$(this).val();
		  k3.push(id_category);
	  });	
	  var data={_token: $('meta[name=csrf-token]').attr('content')
		   ,idserver:idserver
		   ,md5: md5
		   ,id_category:k3.join(",")
		   ,text};
		   $.post('/serverpage_setting_page',data, function(response) {
				        if(response.hasOwnProperty('ok')){
							console.log(response);
							location.reload();
						}
			});
  });
  
});		
</script>	
@endpush		
@endif