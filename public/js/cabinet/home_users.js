var from=0;
var to=0;
var search=0;
var manager=0;
var number=0;
var sorty=0;
var order='summa';
var video_order='summa';
var product_order='summa';
var teaser_order='summa';
var direct='desc';
var video_direct='desc';
var product_direct='desc';
var teaser_direct='desc';
var page=1;
var video_page=1;
var product_page=1;
var teaser_page=1;

function home_users(){
	$.post('/home_users',{ _token: $('meta[name=csrf-token]').attr('content') }, function(response) {
		
		$('#home_users').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		home_all_users(from, to, search, manager, number, order, direct, page);
		home_video_users(from, to, search, manager, number, video_order, video_direct, video_page);
		home_product_users(from, to, search, manager, number, product_order, product_direct, product_page);
		home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page);
		plus();
		video_plus();
		product_plus();
		teaser_plus();
		//общая
		$('#form_for_users').on('click', '#submit', function(event) {
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			search=$('#form_for_users').find('input[name=search]').val();
			from=$('#form_for_users').find('input[name=from]').val();
			to=$('#form_for_users').find('input[name=to]').val();
			manager=$('#form_for_users').find('select[name=manager]').val();
			number=$('#form_for_users').find('select[name=number]').val();
			sorty=$('#form_for_users').find('select[name=sorty]').val();
			page=1;
			video_page=1;
			product_page=1;
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
		});
		
		$('#all_stat').on('click', '.no_active_user', function(){
			var id=$(this).data('set');
			no_active(id);
		});
		
		$('#all_stat').on('click', '.active_user', function(){
			var id=$(this).data('set');
			active(id);
		});
		
		$('#video_stat').on('click', '.no_active_user', function(){
			var id=$(this).data('set');
			no_active(id);
		});
		
		$('#video_stat').on('click', '.active_user', function(){
			var id=$(this).data('set');
			active(id);
		});
		
		$('#product_stat').on('click', '.no_active_user', function(){
			var id=$(this).data('set');
			no_active(id);
		});
		
		$('#product_stat').on('click', '.active_user', function(){
			var id=$(this).data('set');
			active(id);
		});
		
		$('#all_stat').on('click', '.user_lease', function(){
			var id=$(this).data('set');
			lease(id);
		});
		
		$('#all_stat').on('click', '.user_no_lease', function(){
			var id=$(this).data('set');
			nolease(id);
		});
		
		$('#product_stat').on('click', '.user_lease', function(){
			var id=$(this).data('set');
			lease(id);
		});
		
		$('#product_stat').on('click', '.user_no_lease', function(){
			var id=$(this).data('set');
			nolease(id);
		});
		
		$('#video_stat').on('click', '.user_lease', function(){
			var id=$(this).data('set');
			lease(id);
		});
		
		$('#video_stat').on('click', '.user_no_lease', function(){
			var id=$(this).data('set');
			nolease(id);
		});
		
		//суммарная
		$('#all_stat').on('click', '.table_href', function(event) {
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			var old_order=order;
			order=$(this).data('set');
			if (old_order==order){
				if (direct=='desc'){
					direct='asc';
				}
				else{
					direct='desc';
				}
			}
			else{
				direct='desc';
				}
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
		});
		
		$('#all_stat').on('click', '.pagination a', function (e) {
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			page=$(this).attr('href').split('page=')[1];
			e.preventDefault();
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
		});
		
		//видео
		$('#video_stat').on('click', '.table_href', function(event) {
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			var old_order=video_order;
			video_order=$(this).data('set');
			if (old_order==video_order){
				if (video_direct=='desc'){
					video_direct='asc';
				}
				else{
					video_direct='desc';
				}
			}
			else{
				video_direct='desc';
				}
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
		});
		
		$('#video_stat').on('click', '.pagination a', function (e) {
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			video_page=$(this).attr('href').split('page=')[1];
			e.preventDefault();
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
		});
		
		//товарка
		$('#product_stat').on('click', '.table_href', function(event) {
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			var old_order=product_order;
			product_order=$(this).data('set');
			if (old_order==product_order){
				if (product_direct=='desc'){
					product_direct='asc';
				}
				else{
					product_direct='desc';
				}
			}
			else{
				product_direct='desc';
				}
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
		});
		
		$('#product_stat').on('click', '.pagination a', function (e) {
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			product_page=$(this).attr('href').split('page=')[1];
			e.preventDefault();
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
		});
		
		$('#teaser_stat').on('click', '.table_href', function(event) {
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			var old_order=teaser_order;
			teaser_order=$(this).data('set');
			if (old_order==teaser_order){
				if (teaser_direct=='desc'){
					teaser_direct='asc';
				}
				else{
					teaser_direct='desc';
				}
			}
			else{
				teaser_direct='desc';
				}
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
		});
		
		$('#teaser_stat').on('click', '.pagination a', function (e) {
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			teaser_page=$(this).attr('href').split('page=')[1];
			e.preventDefault();
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
		});
	});
};

function lease(id){
	$.post('/user_lease_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function nolease(id){
	$.post('/user_no_lease_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function no_active(id){
	$.post('/user_no_active_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function active(id){
	$.post('/user_active_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function home_all_users(from, to, search, manager, number, order, direct, page, sorty){
	$.post('/home_all_users?page='+page,{ _token: $('meta[name=csrf-token]').attr('content'), 
	from: from, to:to, manager:manager, search:search, number:number, order:order, direct:direct, sorty:sorty}, function(response) {
		$('#all_stat').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
	});
}

function home_video_users(from, to, search, manager, number, order, direct, page, sorty){
	$.post('/home_video_users?page='+page,{ _token: $('meta[name=csrf-token]').attr('content'), 
	from: from, to:to, manager:manager, search:search, number:number, order:order, direct:direct, sorty:sorty}, function(response) {
		$('#video_stat').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
	});
}

function home_product_users(from, to, search, manager, number, order, direct, page, sorty){
	$.post('/home_product_users?page='+page,{ _token: $('meta[name=csrf-token]').attr('content'), 
	from: from, to:to, manager:manager, search:search, number:number, order:order, direct:direct, sorty:sorty}, function(response) {
		$('#product_stat').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
	});
}

function home_teaser_users(from, to, search, manager, number, order, direct, page, sorty){
	$.post('/home_teaser_users?page='+page,{ _token: $('meta[name=csrf-token]').attr('content'), 
	from: from, to:to, manager:manager, search:search, number:number, order:order, direct:direct, sorty:sorty}, function(response) {
		$('#teaser_stat').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
	});
}

function plus(){
	$('#all_stat').on('click', '.plus_all', function(event) {
		var id=$(this).data('set');
		event.preventDefault();
		if ($('#us-'+id).hasClass('in')){
			$('#us-'+id).html('');
		}
		else{
			$.post('/user_detail_widgets/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
				$('#us-'+response.id).html(response.view);
				$('[data-toggle="popover"]').popover({html:true});
				$('[data-toggle="tooltip"]').tooltip();
				$('.default_status').popover({html : true});
				$('#all_users_manager').on('click', '#submit', function(){
					var user_id=$('#all_users_manager').find('input[name=user_id]').val();
					var set_manager=$('#all_users_manager').find('select[name=manager]').val();
					setManager(user_id, set_manager);
				});
				$('#all_users_commission').on('click', '#submit', function(){
					var user_id=$('#all_users_commission').find('input[name=user_id]').val();
					var payment=$('#all_users_commission').find('input[name=payment]').val();
					setCommission(user_id, payment);
				});
				$('#all_stat').on('click', '#add_bottom_site_submit', function(){
					console.log('123');
					var id=$(this).data('set');
					var domain=$('#add_user_site_'+id).find('input[name=domain]').val();
					var types=$('#add_user_site_'+id).find($(":checkbox:checked"));
					var type=[];
					if (types.length>0){
						for (var i=0; i<types.length; i++){
							type[i]=types[i].value;
						}
					}
					var stcurl=$('#add_user_site_'+id).find('input[name=stcurl]').val();
					var stclogin=$('#add_user_site_'+id).find('input[name=stclogin]').val();
					var stcpassword=$('#add_user_site_'+id).find('input[name=stcpassword]').val();
					if (stcurl=="" || domain==""){
						$('#add_user_site_'+id).find($('#pad_zap11')).css('display', 'block');
						$('#add_user_site_'+id).find($('#pad_zap12')).css('display', 'block');
						return;
					}
					add_user_site(id, domain, type, stcurl, stclogin, stcpassword);
				});
				
				$('#all_stat').find($('.pad_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>' +
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='4'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='5'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='6'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='7'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='8'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='9'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='10'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='11'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='12'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="3">Тизерный</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='13'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="3">Тизерный</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='14'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='-1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'+
					'<option value="4">Брендирование</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
						user_parent.find($('.type_for_widget')).css('display', 'none');
						user_parent.find($('.pod_type_for_widget')).html('');
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.save_for_widget')).html(' ');
					}
				});
				
				$('#all_stat').find($('.pod_type_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='3'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='4'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
						user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
							'<option value="1">Автоплей</option>' +
							'<option value="2">Оверлей</option>'+
							'<option value="3">Васт ссылка</option>'+
							'<option value="4">InPage</option>'+
							'<option value="5">Автоплей без звука</option>'+
							'<option value="6">Fly-roll</option>'+
							'<option value="7">Fly-roll без звука</option>'
							);
					}
				});
				
				$('#all_stat').on('click', '#add_user_widget_submit', function(event) {
					var user_parent=$(this).parents('.user_add_widget');
					var pad=user_parent.find('select[name=pad]').val();
					var type=user_parent.find('select[name=type]').val();
					var typeVideo=user_parent.find('select[name=typeVideo]').val();
					var id=user_parent.find('input[name=user_id]').val();
					add_user_widget(id, pad, type, typeVideo);
				});
				
				$('#all_stat').on('click', '#user_dop_status_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var text_for_dop_status=user_parent.find('textarea[name=text_for_dop_status]').val();
					var dop_status=user_parent.find('input:radio[name="dop_status"]:checked').val();
					add_dop_status(id, dop_status, text_for_dop_status);
				});
				
				$('#all_stat').find($('.type_for_commission')).change(function(){
					var user_parent=$(this).parents('.default_commission');
					if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
						user_parent.find($('.video_commission_rus')).css('display', 'block');
						user_parent.find($('.video_commission_cis')).css('display', 'block');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'none');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'6' && user_parent.find($('.type_for_commission option:selected')).val()<'9'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'block');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'8'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'block');
						user_parent.find($('.link_summa_rus')).css('display', 'block');
						user_parent.find($('.link_summa_cis')).css('display', 'block');
					}
				});
				$('#all_stat').on('click', '#default_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var type=user_parent.find('select[name="type"]').val();
					var commission_rus=user_parent.find('select[name="commission_rus"]').val();
					var commission_cis=user_parent.find('select[name="commission_cis"]').val();
					var product_commission=user_parent.find('input[name="product_commission"]').val();
					var link_id=user_parent.find('select[name="link_id"]').val();
					var link_summa_rus=user_parent.find('input[name="link_summa_rus"]').val();
					var link_summa_cis=user_parent.find('input[name="link_summa_cis"]').val();
					add_default(id, type, commission_rus, commission_cis, product_commission, link_id, link_summa_rus, link_summa_cis);
				});
				
				$('#all_stat').on('click', '#destroy_video_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_video_com(id);
				});
				
				$('#all_stat').on('click', '#desctroy_product_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_product_com(id);
				});
				
				$('#all_stat').on('click', '#desctroy_source_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_source_com(id);
				});
				
				$('#all_stat').on('click', '.delete_widget', function(){
					var id=$(this).data('set');
					delete_widget(id);
				});
				
			});
		}
	});
}

function delete_widget(id){
	$.post('/widget/delete_post/'+id,{ _token: $('meta[name=csrf-token]').attr('content')}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function desctroy_video_com(id){
	$.post('/user_for_video_default_delete_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id: id}, function(response) {
		if (response.ok){
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function desctroy_product_com(id){
	$.post('/user_for_product_default_delete_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id: id}, function(response) {
		if (response.ok){
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function desctroy_source_com(id){
	$.post('/user_control_summa_delete_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id: id}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function add_default(id, type, commission_rus, commission_cis, product_commission, link_id, link_summa_rus, link_summa_cis){
	$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
	$.post('/user_for_default_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: id,
	type: type, 
	commission_rus: commission_rus, 
	commission_cis: commission_cis, 
	product_commission: product_commission, 
	link_id: link_id, 
	link_summa_rus: link_summa_rus, 
	link_summa_cis:link_summa_cis}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function add_dop_status(id, dop_status, text_for_dop_status){
	$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
	$.post('/user_for_dop_status_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: id,
	dop_status: dop_status,
	text_for_dop_status: text_for_dop_status}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function add_user_widget(id, pad, type, typeVideo){
	$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
	id=id;
	pad=pad;
	type=type;
	typeVideo=typeVideo;
	$.post('/widget/create_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: id,
	pad: pad,
	type: type,
	typeVideo: typeVideo}, function(response) {
		window.location.replace(response.to);
	});
}
function setManager(user_id, set_manager){
	$.post('/user_for_manager_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	manager: set_manager,}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function add_user_site(id, domain, type, stcurl, stclogin, stcpassword){
	$.post('/add_pads_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: id,
	domain: domain,
	type: type,
	stcurl: stcurl,
	stclogin: stclogin,
	stcpassword: stcpassword}, function(response) {
		$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function setCommission(user_id, payment){
	$.post('/payment_commission_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	payment: payment,}, function(response) {
		if (response.ok){
			$('#all_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#video_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#product_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			$('#teaser_stat').html('<div class="affiliate_cabinet_block" style="margin-top: 10px;">'+
			'<div class="loaded">'+
			'</div>'+
			'</div>');
			home_all_users(from, to, search, manager, number, order, direct, page, sorty);
			home_video_users(from, to, search, manager, number, video_order, video_direct, video_page, sorty);
			home_product_users(from, to, search, manager, number, product_order, product_direct, product_page, sorty);
			home_teaser_users(from, to, search, manager, number, teaser_order, teaser_direct, teaser_page, sorty);
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 1 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
		else{
			$.post('/home_message',{ _token: $('meta[name=csrf-token]').attr('content'), message: response.message, status: 2 }, function(response) {
				$('#home_message').html(response.view);
			});
		}
	});
}

function video_plus(){
	$('#video_stat').on('click', '.plus_video', function(event) {
		var id=$(this).data('set');
		event.preventDefault();
		if ($('#v-'+id).hasClass('in')){
			$('#v-'+id).html('');
		}
		else{
			$.post('/user_detail_widgets_video/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
			$('#v-'+response.id).html(response.view);
			$('[data-toggle="popover"]').popover({html:true});
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
			$('#all_users_manager').on('click', '#submit', function(){
				var user_id=$('#all_users_manager').find('input[name=user_id]').val();
				var set_manager=$('#all_users_manager').find('select[name=manager]').val();
				setManager(user_id, set_manager);
			});
			$('#all_users_commission').on('click', '#submit', function(){
				var user_id=$('#all_users_commission').find('input[name=user_id]').val();
				var payment=$('#all_users_commission').find('input[name=payment]').val();
				setCommission(user_id, payment);
			});
			$('#video_stat').on('click', '#add_bottom_site_submit', function(){
					console.log('123');
					var id=$(this).data('set');
					var domain=$('#add_user_site_video_'+id).find('input[name=domain]').val();
					var types=$('#add_user_site_video_'+id).find($(":checkbox:checked"));
					var type=[];
					if (types.length>0){
						for (var i=0; i<types.length; i++){
							type[i]=types[i].value;
						}
					}
					var stcurl=$('#add_user_site_video_'+id).find('input[name=stcurl]').val();
					var stclogin=$('#add_user_site_video_'+id).find('input[name=stclogin]').val();
					var stcpassword=$('#add_user_site_video_'+id).find('input[name=stcpassword]').val();
					if (stcurl=="" || domain==""){
						$('#add_user_site_video_'+id).find($('#pad_zap11')).css('display', 'block');
						$('#add_user_site_video_'+id).find($('#pad_zap12')).css('display', 'block');
						return;
					}
					add_user_site(id, domain, type, stcurl, stclogin, stcpassword);
				});
			$('#video_stat').find($('.pad_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>' +
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='4'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='5'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='6'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					if (user_parent.find($('#role')).data('role')=='1'){
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					}
					else{
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="6">Fly-roll</option>'
						);
					}
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='-1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
						user_parent.find($('.type_for_widget')).css('display', 'none');
						user_parent.find($('.pod_type_for_widget')).html('');
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.save_for_widget')).html(' ');
					}
				});
				
				$('#video_stat').find($('.pod_type_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='3'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
						user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
							'<option value="1">Автоплей</option>' +
							'<option value="2">Оверлей</option>'+
							'<option value="3">Васт ссылка</option>'+
							'<option value="4">InPage</option>'+
							'<option value="5">Автоплей без звука</option>'+
							'<option value="6">Fly-roll</option>'+
							'<option value="7">Fly-roll без звука</option>'
							);
					}
				});
				
				$('#video_stat').on('click', '#add_user_widget_submit', function(event) {
					var user_parent=$(this).parents('.user_add_widget');
					var pad=user_parent.find('select[name=pad]').val();
					var type=user_parent.find('select[name=type]').val();
					var typeVideo=user_parent.find('select[name=typeVideo]').val();
					var id=user_parent.find('input[name=user_id]').val();
					add_user_widget(id, pad, type, typeVideo);
				});
				$('#video_stat').on('click', '#user_dop_status_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var text_for_dop_status=user_parent.find('textarea[name=text_for_dop_status]').val();
					var dop_status=user_parent.find('input:radio[name="dop_status"]:checked').val();
					add_dop_status(id, dop_status, text_for_dop_status);
				});
				
				$('#video_stat').find($('.type_for_commission')).change(function(){
					var user_parent=$(this).parents('.default_commission');
					if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
						user_parent.find($('.video_commission_rus')).css('display', 'block');
						user_parent.find($('.video_commission_cis')).css('display', 'block');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'none');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'6' && user_parent.find($('.type_for_commission option:selected')).val()<'9'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'block');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'8'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'block');
						user_parent.find($('.link_summa_rus')).css('display', 'block');
						user_parent.find($('.link_summa_cis')).css('display', 'block');
					}
				});
				$('#video_stat').on('click', '#default_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var type=user_parent.find('select[name="type"]').val();
					var commission_rus=user_parent.find('select[name="commission_rus"]').val();
					var commission_cis=user_parent.find('select[name="commission_cis"]').val();
					var product_commission=user_parent.find('input[name="product_commission"]').val();
					var link_id=user_parent.find('select[name="link_id"]').val();
					var link_summa_rus=user_parent.find('input[name="link_summa_rus"]').val();
					var link_summa_cis=user_parent.find('input[name="link_summa_cis"]').val();
					add_default(id, type, commission_rus, commission_cis, product_commission, link_id, link_summa_rus, link_summa_cis);
				});
				
				$('#video_stat').on('click', '#destroy_video_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_video_com(id);
				});
				
				$('#video_stat').on('click', '#desctroy_product_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_product_com(id);
				});
				
				$('#video_stat').on('click', '#desctroy_source_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_source_com(id);
				});
				
				$('#video_stat').on('click', '.delete_widget', function(){
					var id=$(this).data('set');
					delete_widget(id);
				});
			});
		}
	});
}

function product_plus(){
	$('#product_stat').on('click', '.plus_product', function(event) {
		var id=$(this).data('set');
		event.preventDefault();
		if ($('#p-'+id).hasClass('in')){
			$('#p-'+id).html('');
		}
		else{
			$.post('/user_detail_widgets_product/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
			$('#p-'+response.id).html(response.view);
			$('[data-toggle="popover"]').popover({html:true});
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
			$('#all_users_manager').on('click', '#submit', function(){
					var user_id=$('#all_users_manager').find('input[name=user_id]').val();
					var set_manager=$('#all_users_manager').find('select[name=manager]').val();
					setManager(user_id, set_manager);
				});
			$('#all_users_commission').on('click', '#submit', function(){
					var user_id=$('#all_users_commission').find('input[name=user_id]').val();
					var payment=$('#all_users_commission').find('input[name=payment]').val();
					setCommission(user_id, payment);
				});
			$('#product_stat').on('click', '#add_bottom_site_submit', function(){
					console.log('123');
					var id=$(this).data('set');
					var domain=$('#add_user_site_'+id).find('input[name=domain]').val();
					var types=$('#add_user_site_'+id).find($(":checkbox:checked"));
					var type=[];
					if (types.length>0){
						for (var i=0; i<types.length; i++){
							type[i]=types[i].value;
						}
					}
					var stcurl=$('#add_user_site_'+id).find('input[name=stcurl]').val();
					var stclogin=$('#add_user_site_'+id).find('input[name=stclogin]').val();
					var stcpassword=$('#add_user_site_'+id).find('input[name=stcpassword]').val();
					if (stcurl=="" || domain==""){
						$('#add_user_site_'+id).find($('#pad_zap11')).css('display', 'block');
						$('#add_user_site_'+id).find($('#pad_zap12')).css('display', 'block');
						return;
					}
					add_user_site(id, domain, type, stcurl, stclogin, stcpassword);
				});
			$('#product_stat').find($('.pad_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>' +
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='4'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='5'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='6'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					if (user_parent.find($('#role')).data('role')=='1'){
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					}
					else{
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="6">Fly-roll</option>'
						);
					}
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='-1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
						user_parent.find($('.type_for_widget')).css('display', 'none');
						user_parent.find($('.pod_type_for_widget')).html('');
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.save_for_widget')).html(' ');
					}
				});
				
				$('#product_stat').find($('.pod_type_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='3'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
						user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
							'<option value="1">Автоплей</option>' +
							'<option value="2">Оверлей</option>'+
							'<option value="3">Васт ссылка</option>'+
							'<option value="4">InPage</option>'+
							'<option value="5">Автоплей без звука</option>'+
							'<option value="6">Fly-roll</option>'+
							'<option value="7">Fly-roll без звука</option>'
							);
					}
				});
				
				$('#product_stat').on('click', '#add_user_widget_submit', function(event) {
					var user_parent=$(this).parents('.user_add_widget');
					var pad=user_parent.find('select[name=pad]').val();
					var type=user_parent.find('select[name=type]').val();
					var typeVideo=user_parent.find('select[name=typeVideo]').val();
					var id=user_parent.find('input[name=user_id]').val();
					add_user_widget(id, pad, type, typeVideo);
				});
				
				$('#product_stat').on('click', '#user_dop_status_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var text_for_dop_status=user_parent.find('textarea[name=text_for_dop_status]').val();
					var dop_status=user_parent.find('input:radio[name="dop_status"]:checked').val();
					add_dop_status(id, dop_status, text_for_dop_status);
				});
				
				$('#product_stat').find($('.type_for_commission')).change(function(){
					var user_parent=$(this).parents('.default_commission');
					if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
						user_parent.find($('.video_commission_rus')).css('display', 'block');
						user_parent.find($('.video_commission_cis')).css('display', 'block');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'none');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'6' && user_parent.find($('.type_for_commission option:selected')).val()<'9'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'block');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'8'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'block');
						user_parent.find($('.link_summa_rus')).css('display', 'block');
						user_parent.find($('.link_summa_cis')).css('display', 'block');
					}
				});
				$('#product_stat').on('click', '#default_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var type=user_parent.find('select[name="type"]').val();
					var commission_rus=user_parent.find('select[name="commission_rus"]').val();
					var commission_cis=user_parent.find('select[name="commission_cis"]').val();
					var product_commission=user_parent.find('input[name="product_commission"]').val();
					var link_id=user_parent.find('select[name="link_id"]').val();
					var link_summa_rus=user_parent.find('input[name="link_summa_rus"]').val();
					var link_summa_cis=user_parent.find('input[name="link_summa_cis"]').val();
					add_default(id, type, commission_rus, commission_cis, product_commission, link_id, link_summa_rus, link_summa_cis);
				});
				
				$('#product_stat').on('click', '#destroy_video_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_video_com(id);
				});
				
				$('#product_stat').on('click', '#desctroy_product_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_product_com(id);
				});
				
				$('#product_stat').on('click', '#desctroy_source_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_source_com(id);
				});
				
				$('#product_stat').on('click', '.delete_widget', function(){
					var id=$(this).data('set');
					delete_widget(id);
				});
				
			});
			
		}
	});
}

function teaser_plus(){
	$('#teaser_stat').on('click', '.plus_teaser', function(event) {
		var id=$(this).data('set');
		event.preventDefault();
		if ($('#t-'+id).hasClass('in')){
			$('#t-'+id).html('');
		}
		else{
			$.post('/user_detail_widgets_teaser/'+id,{ _token: $('meta[name=csrf-token]').attr('content'), from: $('#from_for_users').val(), to:$('#to_for_users').val()}, function(response) {
			$('#t-'+response.id).html(response.view);
			$('[data-toggle="popover"]').popover({html:true});
			$('[data-toggle="tooltip"]').tooltip();
			$('.default_status').popover({html : true});
			$('#all_users_manager').on('click', '#submit', function(){
					var user_id=$('#all_users_manager').find('input[name=user_id]').val();
					var set_manager=$('#all_users_manager').find('select[name=manager]').val();
					setManager(user_id, set_manager);
				});
			$('#all_users_commission').on('click', '#submit', function(){
					var user_id=$('#all_users_commission').find('input[name=user_id]').val();
					var payment=$('#all_users_commission').find('input[name=payment]').val();
					setCommission(user_id, payment);
				});
			$('#teaser_stat').on('click', '#add_bottom_site_submit', function(){
					console.log('123');
					var id=$(this).data('set');
					var domain=$('#add_user_site_'+id).find('input[name=domain]').val();
					var types=$('#add_user_site_'+id).find($(":checkbox:checked"));
					var type=[];
					if (types.length>0){
						for (var i=0; i<types.length; i++){
							type[i]=types[i].value;
						}
					}
					var stcurl=$('#add_user_site_'+id).find('input[name=stcurl]').val();
					var stclogin=$('#add_user_site_'+id).find('input[name=stclogin]').val();
					var stcpassword=$('#add_user_site_'+id).find('input[name=stcpassword]').val();
					if (stcurl=="" || domain==""){
						$('#add_user_site_'+id).find($('#pad_zap11')).css('display', 'block');
						$('#add_user_site_'+id).find($('#pad_zap12')).css('display', 'block');
						return;
					}
					add_user_site(id, domain, type, stcurl, stclogin, stcpassword);
				});
			$('#teaser_stat').find($('.pad_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>' +
					'<option value="2">Видео</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='4'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='5'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарка</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='6'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.type_for_video')).css('display', 'block');
					if (user_parent.find($('#role')).data('role')=='1'){
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="3">Васт ссылка</option>'+
						'<option value="4">InPage</option>'+
						'<option value="5">Автоплей без звука</option>'+
						'<option value="6">Fly-roll</option>'+
						'<option value="7">Fly-roll без звука</option>'
						);
					}
					else{
						user_parent.find($('.type_for_video_select')).html(
						'<option value="1">Автоплей</option>' +
						'<option value="2">Оверлей</option>'+
						'<option value="6">Fly-roll</option>'
						);
					}
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='-1'){
					user_parent.find($('.type_for_widget')).css('display', 'block');
					user_parent.find($('.pod_type_for_widget')).html(
					'<option value="1">Товарный</option>'+
					'<option value="2">Видео</option>'+
					'<option value="3">Тизерный</option>'
					);
					user_parent.find($('.save_for_widget')).html(
					'<a id="add_user_widget_submit" class="btn btn-primary">Сохранить</a>'
					);
					}
					else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
						user_parent.find($('.type_for_widget')).css('display', 'none');
						user_parent.find($('.pod_type_for_widget')).html('');
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.save_for_widget')).html(' ');
					}
				});
				
				$('#teaser_stat').find($('.pod_type_for_widget')).change(function(){
					var user_parent=$(this).parents('.user_add_widget');
					if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='1'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='3'){
						user_parent.find($('.type_for_video')).css('display', 'none');
						user_parent.find($('.type_for_video_select')).html('');
					}
					else if (user_parent.find($('.pod_type_for_widget option:selected')).val()=='2'){
						user_parent.find($('.type_for_video')).css('display', 'block');
						user_parent.find($('.type_for_video_select')).html(
							'<option value="1">Автоплей</option>' +
							'<option value="2">Оверлей</option>'+
							'<option value="3">Васт ссылка</option>'+
							'<option value="4">InPage</option>'+
							'<option value="5">Автоплей без звука</option>'+
							'<option value="6">Fly-roll</option>'+
							'<option value="7">Fly-roll без звука</option>'
							);
					}
				});
				
				$('#teaser_stat').on('click', '#add_user_widget_submit', function(event) {
					var user_parent=$(this).parents('.user_add_widget');
					var pad=user_parent.find('select[name=pad]').val();
					var type=user_parent.find('select[name=type]').val();
					var typeVideo=user_parent.find('select[name=typeVideo]').val();
					var id=user_parent.find('input[name=user_id]').val();
					add_user_widget(id, pad, type, typeVideo);
				});
				
				$('#teaser_stat').on('click', '#user_dop_status_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var text_for_dop_status=user_parent.find('textarea[name=text_for_dop_status]').val();
					var dop_status=user_parent.find('input:radio[name="dop_status"]:checked').val();
					add_dop_status(id, dop_status, text_for_dop_status);
				});
				
				$('#teaser_stat').find($('.type_for_commission')).change(function(){
					var user_parent=$(this).parents('.default_commission');
					if (user_parent.find($('.type_for_commission option:selected')).val()>'0' && user_parent.find($('.type_for_commission option:selected')).val()<'7'){
						user_parent.find($('.video_commission_rus')).css('display', 'block');
						user_parent.find($('.video_commission_cis')).css('display', 'block');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()=='0'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'none');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'6' && user_parent.find($('.type_for_commission option:selected')).val()<'9'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'block');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'none');
						user_parent.find($('.link_summa_rus')).css('display', 'none');
						user_parent.find($('.link_summa_cis')).css('display', 'none');
					}
					if (user_parent.find($('.type_for_commission option:selected')).val()>'8'){
						user_parent.find($('.video_commission_rus')).css('display', 'none');
						user_parent.find($('.video_commission_cis')).css('display', 'none');
						user_parent.find($('.product_commission')).css('display', 'none');
						user_parent.find($('.commission_save')).css('display', 'block');
						user_parent.find($('.link_select')).css('display', 'block');
						user_parent.find($('.link_summa_rus')).css('display', 'block');
						user_parent.find($('.link_summa_cis')).css('display', 'block');
					}
				});
				$('#teaser_stat').on('click', '#default_submit', function(){
					var user_parent=$(this).parents('.modal-content');
					var id=$(this).data('set');
					var type=user_parent.find('select[name="type"]').val();
					var commission_rus=user_parent.find('select[name="commission_rus"]').val();
					var commission_cis=user_parent.find('select[name="commission_cis"]').val();
					var product_commission=user_parent.find('input[name="product_commission"]').val();
					var link_id=user_parent.find('select[name="link_id"]').val();
					var link_summa_rus=user_parent.find('input[name="link_summa_rus"]').val();
					var link_summa_cis=user_parent.find('input[name="link_summa_cis"]').val();
					add_default(id, type, commission_rus, commission_cis, product_commission, link_id, link_summa_rus, link_summa_cis);
				});
				
				$('#teaser_stat').on('click', '#destroy_video_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_video_com(id);
				});
				
				$('#teaser_stat').on('click', '#desctroy_product_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_product_com(id);
				});
				
				$('#teaser_stat').on('click', '#desctroy_source_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_source_com(id);
				});
				
				$('#teaser_stat').on('click', '.delete_widget', function(){
					var id=$(this).data('set');
					delete_widget(id);
				});
				
			});
			
		}
	});
}