{{ $collection->appends([])->links() }}
<div class="affiliate_cabinet_block" style="height: 550px;">
<div class="affiliate_cabinet_block" style="height: 550px;">

<div class="heading text-left">Мои Счета </div> 
<hr class="affilaite_hr"> 
<div id="home_widgets" class="home_block">
<div class="row" style="margin: 10px 0">
   <div class="affiliate_cabinet_bot" style="margin-top: 10px;">
   <table class="table table-condensed table-hover widget-table">
		<thead>
			<tr class="widget-table-header">
				<td style="width:10px">№</td>
				<td style="width:95px">дата</td>
				<td style="width:85px">статус</td>
				<td style="width:125px">платежка</td>
				<td>сумма</td>
			    <td>дата оплаты</td>
				<td colspan="3">документы</td>
				
			</tr>
		</thead>
		<tbody>
		@foreach($collection as $col)
		@php
		$k='ls /home/www/widget.market-place.su/public/files/'.$user->id.'/acts/'.$col->id.'[.]*';
		$file='';
		$row = exec($k,$output,$error);
		while(list(,$row) = each($output)){
			$file=str_replace('/home/www/widget.market-place.su/public','',$row);
			$file='/adv_/download?url='.rawurlencode($file);
         //echo $file, "<BR>\n";
         }
		$fact='';
		$k='ls /home/www/widget.market-place.su/public/files/'.$user->id.'/schetfaktura/'.$col->id.'[.]*'; 
		$row = exec($k,$output,$error);
		while(list(,$row) = each($output)){
			$fact=str_replace('/home/www/widget.market-place.su/public','',$row);
			$fact='/adv_/download?url='.rawurlencode($fact);
         //echo $file, "<BR>\n";
         }
		$config['wparams']['id']=$col->id;
		@endphp
				<tr @if($col->paymenede=='Оплачен') class ="success" @else @if($col->paymenede!='Авансовый') class ="danger"   @endif @endif>
				<td>{{$col->number}}</td>
				<td>{{$col->day}}</td>
				<td>
				@role(['admin','super_manager','manager'])
				
						<select class="pleer_stop" data-set="{{$col->id}}">
						<option value=1 @if($col->paymenede=='Неоплачен') selected @endif>Неоплачен</option>
						<option value=2 @if($col->paymenede=='Оплачен') selected @endif>Оплачен</option>
						<option value=3 @if($col->paymenede=='Авансовый') selected @endif>Авансовый</option>
						<option value=4 @if($col->paymenede=='Удалён') selected @endif>Удалён</option>
					</select>
                      @else {{$col->paymenede}}
					@endrole
					</td>
					<td>
					@role(['admin','super_manager','manager'])
					<input class ="nomer_plateja" type="text" value="{{$col->npp}}" style="width:75px;margin-right:4px"><span class="glyphicon glyphicon-refresh send_oplata"></span
					  @else {{$col->npp}}
					@endrole
					</td>
				 <td>{{$col->total}}</td>
				<td class ="opaopl">@if($col->payd)оплачен {{$col->payd}}@else оплатить до {{$col->payment_date}}@endif</td>
				<td style="width:25px;"><a href="{{route($config['pref'].'invoice_view',$config['wparams'])}}"><span class="glyphicon glyphicon-zoom-in" title="распечатать"></span></a></td>
				<td style="width:25px;">
				@if($fact)
				<a href="{{$fact}}"><span class="glyphicon glyphicon-check" title="акт приёмки"></span></a>
			    @endif
				</td>
				
				<td style="width:25px;">
				@if($file)
				<a href="{{$file}}" target="_blank"><span class="glyphicon glyphicon-flag" title="акт выполнения"></span></a>
			     @endif
				</td>

			</tr>
		@endforeach
		</tbody>
   </table>
   </div>
</div>
</div>
</div>
</div>
@push('cabinet_home_js')
	<script>
		$(document).ready(function(){
			$('.send_oplata').click(function(){
				var val=$(this).closest('tr').find("option:selected").val();
				var id=$(this).closest('tr').find("select").data('set');
				if(val==2){
				var nombre=$(this).closest('tr').find("input.nomer_plateja" ).val();
				if(!nombre){
					alert('укажите номер платежного документа');
					return;
				}
				}
				
				var mytr=$(this).closest('tr');
				$.post('/adv_/invoice_status_post/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), 
					status: val,nombre:nombre}, function(response) {
						if(response.hasOwnProperty('ok')){
							 mytr.removeClass();
							switch(val){
								case "1":
								mytr.addClass("danger");
								mytr.find("input.nomer_plateja" ).val('');
								break;
								case "2":
								mytr.addClass("success");
								mytr.find("td.opaopl" ).html('оплачен только что');
								break;
								case "3":
								break;
								case "4":
								break;
							}
						}
							
							//alert('ok')
						console.log(['responze',response]);
					});
				
			});
			$('.pleer_stop').change(function(){
				var val=$('option:selected',this).val();
				var id=$(this).data('set')
				if(val==2){
				var nombre=$(this).closest('tr').find("input.nomer_plateja" ).val();
				if(!nombre){
					alert('укажите номер платежного документа');
					return;
				}
				}
				var mytr=$(this).closest('tr');
				
				$.post('/adv_/invoice_status_post/'+$(this).data('set'),{ _token: $('meta[name=csrf-token]').attr('content'), 
					status: val,nombre:nombre}, function(response) {
					if(response.hasOwnProperty('ok')){
							mytr.removeClass();
							switch(val){
								case "1":
								mytr.addClass("danger");
								mytr.find("input.nomer_plateja" ).val('');
								break;
								case "2":
								mytr.addClass("success");
								mytr.find("td.opaopl" ).html('оплачен только что');
								break;
								case "3":
								break;
								case "4":
								break;
							}
						}
						console.log(['responze',response]);
					});
			});
		});
	</script>
@endpush		
