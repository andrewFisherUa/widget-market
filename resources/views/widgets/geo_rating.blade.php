
  @push('cabinet_home_top')
  <style>
  
  .openCategory > th{
	            position: -webkit-sticky;
				position: -moz-sticky;
				position: -ms-sticky;
				position: -o-sticky;
				position: sticky;
                top: 0;
				background: rgba(161,21,207, 0.5);
				color: #FFF;
  }
   td.row_back{
	  background: rgba(158,207,195, 0.3);
  }
  td.row_tuk{
	  background: rgba(161,21,207, 0.5);
  }
  </style>
   @endpush
   @push('cabinet_home_js')
  <script>
  $( document).ready(function() {
	
	   $(".dataopenclick").each(function(){
		   $(this).click(function(event){
			    var target = $( event.target );
				if(target.hasClass("glyphicon-triangle-right")){
					target.toggleClass('glyphicon-triangle-right glyphicon-triangle-bottom');
					$(".geo_regional_"+target.data("id")).show();
					
				}else{
					target.toggleClass('glyphicon-triangle-bottom glyphicon-triangle-right');
					$(".geo_regional_"+target.data("id")).hide();
				}
			   // alert(target.data("id"));
		   });
		   console.log($(this));
	   });
  // Handler for .ready() called.
  });
  </script>
  @endpush
  @if ($config["id"]!==0 && $some_soobr==1)
	  <input type="hidden" name ="pricegame" value ="1">
  @endif	  
  <table class="table table-sm">
    <thead>
      <tr class ="openCategory">
        <th>Страна</th>
        <th>Регион</th>
		@if ($config["id"]!==0)<th>Показывать</th>@endif
		@foreach($ccx as $cx)
        <th>{{$cx->name}}</th>
        @endforeach
		<th>Другое</th>		
		
      </tr>
    </thead>
    <tbody>
	@foreach($ipgs[0] as $gp)
      <tr>
        <td>{{$gp->name}} / @if(isset($ipgs[$gp->id])) <span class="glyphicon glyphicon-triangle-right dataopenclick" data-id="{{$gp->id}}" style="cursor:pointer"></span> @endif</td>
        <td></td>
		@if ($config["id"]!==0)<td>
		
		<input type="checkbox" @if(isset($rates[$gp->id])) checked @endif  name="vkl[{{$gp->id}}]" value="1">
		</td>@endif
		@foreach($ccx as $cx)
			<td
			@if(isset($rates[$gp->id][$cx->id]))  class ="row_tuk" @else  @if(isset($cols[$cx->id]) || isset($rows[$gp->id])) class ="row_back" @endif @endif>
			@if ($some_soobr==1)
				<input type="text" class="form-control input-sm" name ="pps[{{$gp->id}}][{{$cx->id}}]" @if(isset($monorating[$gp->id][$cx->id]) && $monorating[$gp->id][$cx->id]["price"]) value ="{{$monorating[$gp->id][$cx->id]["price"]}}" @endif>
		@else	
			    <div>@if(isset($monorating[$gp->id][$cx->id]) && $monorating[$gp->id][$cx->id]["price"]){{$monorating[$gp->id][$cx->id]["price"]}} @endif</div>
		@endif

			</td>
		@endforeach
		<td  @if(isset($rates[$gp->id][0])) class ="row_tuk" @else @if(isset($cols[0])  || isset($rows[$gp->id])) class ="row_back" @endif @endif>
		@if ($some_soobr==1)
				<input type="text" class="form-control input-sm" name ="pps[{{$gp->id}}][0]" @if(isset($monorating[$gp->id][0]) && $monorating[$gp->id][0]["price"]) value ="{{$monorating[$gp->id][0]["price"]}}" @endif>
		@else	
			 <div>@if(isset($monorating[$gp->id][0]) && $monorating[$gp->id][$cx->id]["price"]){{$monorating[$gp->id][0]["price"]}} @endif</div>
		@endif
		</td>

      </tr>
	  @if(isset($ipgs[$gp->id]))
		  	@foreach($ipgs[$gp->id] as $vp)
		
		   <tr class="geo_regional_{{$gp->id}}" style="display:none">
        <td></td>
        <td>{{$vp->name}}</td>
		@if ($config["id"]!==0)<td>
		<input type="checkbox" name="vkl[{{$vp->id}}]" @if(isset($rates[$vp->id])) checked @endif  name="vkl[{{$gp->id}}]" value="1">
		</td>@endif
			  @foreach($ccx as $cx)
		    <td @if(isset($rates[$vp->id][$cx->id]))  class ="row_tuk" @else @if(isset($cols[$cx->id]) || isset($rows[$vp->id])) class ="row_back" @endif @endif >
			@if ($some_soobr==1)
				<input type="text" class="form-control input-sm" name ="pps[{{$vp->id}}][{{$cx->id}}]" @if(isset($monorating[$vp->id][$cx->id]) && $monorating[$vp->id][$cx->id]["price"]) value ="{{$monorating[$vp->id][$cx->id]["price"]}}" @endif>
		     @else	
				 <div>@if(isset($monorating[$vp->id][$cx->id]) && $monorating[$vp->id][$cx->id]["price"]){{$monorating[$vp->id][$cx->id]["price"]}} @endif</div>
		     @endif
		   </td>
		  @endforeach
           <td @if(isset($rates[$vp->id][0]))  class ="row_tuk" @else @if(isset($cols[0])  || isset($rows[$vp->id])) class ="row_back" @endif @endif>
		   @if ($some_soobr==1)
				<input type="text" class="form-control input-sm" name ="pps[{{$vp->id}}][0]" @if(isset($monorating[$vp->id][0]) && $monorating[$vp->id][0]["price"]) value ="{{$monorating[$vp->id][0]["price"]}}" @endif>
		     @else	
				  <div>@if(isset($monorating[$vp->id][0]) && $monorating[$vp->id][0]["price"]){{$monorating[$vp->id][0]["price"]}} @endif</div>
		  @endif
		   </td>


      </tr>
	       @endforeach
	  @endif	  
	  @endforeach
    </tbody>
  </table>	  
 