<div id="affiliate_all_pads">
	@if (count($partnerPads)>0)
		@foreach ($partnerPads as $pad)
			<div class="affiliate_pad" style="position: relative" >
				<div data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="{{$pad->domain}}" class="affiliate_all_pads_domain">
					{{$pad->domain}}
				</div>
				@if ($pad->status==0)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="На модерации" class="glyphicon glyphicon-time affiliate_all_pads_domain_gliph blue"></span>
				@elseif ($pad->status==2)
					<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="Отклонена" class="glyphicon glyphicon-remove-circle affiliate_all_pads_domain_gliph red"></span>
				@elseif ($pad->status==1)
					@if ($pad->type==-1 or $pad->type==1 or $pad->type==3 or $pad->type==5 or $pad->type==7 or $pad->type==9 or $pad->type==11 or $pad->type==13)
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="Одобрена на товарный виджет" class="glyphicon glyphicon glyphicon-shopping-cart affiliate_all_pads_domain_gliph green"></span>
					@endif
					@if ($pad->type==-1 or $pad->type==2 or $pad->type==3 or $pad->type==6 or $pad->type==7 or $pad->type==10 or $pad->type==11 or $pad->type==14)
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="Одобрена на видео виджет" class="glyphicon glyphicon glyphicon glyphicon-facetime-video affiliate_all_pads_domain_gliph green"></span>
					@endif
					@if ($pad->type==-1 or $pad->type==4 or $pad->type==5 or $pad->type==6 or $pad->type==7 or $pad->type==12 or $pad->type==13 or $pad->type==14)
						<span data-toggle="tooltip" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="Одобрена на тизерный виджет" class="glyphicon glyphicon glyphicon glyphicon-th-large affiliate_all_pads_domain_gliph green"></span>
					@endif
					@if ($pad->type==-1 or $pad->type==8 or $pad->type==9 or $pad->type==10 or $pad->type==11 or $pad->type==12 or $pad->type==13 or $pad->type==14)
						<span data-toggle="tooltip" data-placement="bottom" style="font-size: 18px; margin: 0 2px;" title="Одобрена на брендирование" class="glyphicon glyphicon-picture affiliate_all_pads_domain_gliph green"></span>
					@endif
				@endif
					@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime(date('2018-01-01')))
						@if ((strtotime($pad->setUser($pad->user_id)->created_at)<strtotime(date('2017-12-01'))
							and 
							strtotime($pad->created_at)>strtotime(date('2017-12-01'))) 
							or ($pad->userProfile($pad->user_id)->referer 
							and 
							strtotime($pad->created_at)>strtotime(date('2017-12-01'))))
						<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
						color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'Xgv2Z88CX7ep')->where('user_id', $pad->user_id)->first())
						@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime($pad->setUser($pad->user_id)->created_at)+3600*24*14)
							<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
							color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					@if ($dop=\DB::connection()->table('sponsored_links_regis')->where('affiliate', 'PjnoPNlN6NN3')->where('user_id', $pad->user_id)->first())
						@if ($pad->status==1 and strtotime(date('Y-m-d'))<strtotime($pad->setUser($pad->user_id)->created_at)+3600*24*14)
							<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="+10% к доходу" style="font-weight: bold; 
							color: #fff; background: #c30000; padding: 0 5px; cursor: pointer; border-radius: 20px; position: relative; top: -5px">+10%</span>
						@endif
					@endif
					
				@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
				<a href="{{route('pads.edit', ['id'=>$pad->id] )}}" target="_blank" style="cursor:pointer;">
					<span data-toggle="tooltip" data-trigger="hover" data-placement="left" title="Редактировать" style="top: 8px" class="glyphicon glyphicon glyphicon glyphicon glyphicon-cog affiliate_all_pads_domain_gliph blue pads_config"></span>
				</a>
				@else
				<a data-toggle="modal" data-target="#edit_affiliate_domain_{{$pad->id}}" style="cursor:pointer;">
					<span data-toggle="tooltip" data-trigger="hover" data-placement="left" title="Редактировать" style="top: 8px" class="glyphicon glyphicon glyphicon glyphicon glyphicon-cog affiliate_all_pads_domain_gliph blue pads_config"></span>
				</a>
				@endif
			</div>
			@include('common.cabinet.modal.edit_domain')
		@endforeach
	@else
		<div class="no_manager text-center">После добавления площадки, она будет отображаться здесь. Для добавления площадки нажмите на зеленый плюс в этом блоке.</div>
	@endif
</div>
@include('common.cabinet.modal.add_domain')