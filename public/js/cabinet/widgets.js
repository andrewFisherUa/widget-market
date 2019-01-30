var from=0;
var to=0;

function widgets(from, to){
	
	
	var from=from || 0;
	var to=to || 0;
	
    //alert($('#user_id').html());
	//$.get('/home_widgets',{id_user: $('#user_id').html()}, function(response) {
	//	alert(response)
	//});	
		
	
	$.post('/home_widgets',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html(), from: from, to: to }, function(response) {
	
		$('#home_widgets').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		var script = document.createElement('script');
		script.src = "/js/custom_scroll/jquery.custom-scroll.min.js"
		document.body.appendChild(script);
		script.onload = function() {
			$('#affiliate_all_widgets').customScroll({
				offsetTop: 78,
				offsetRight: 16,
				offsetBottom: -78,
				vertical: true,
				horizontal: false
			});
		}
		var momentscript=document.createElement('script');
		momentscript.src = "/js/daterange/moment.js"
		document.body.appendChild(momentscript);
		momentscript.onload = function(){
			var datescript = document.createElement('script');
			datescript.src = "/js/daterange/daterangepicker.js"
			document.body.appendChild(datescript);
			datescript.onload = function(){
				 $('input[name="from"]').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true,
					"locale": {
					"format": "YYYY-MM-DD",
					"separator": " - ",
					"applyLabel": "Применить",
					"cancelLabel": "Отмена",
					"fromLabel": "От",
					"toLabel": "До",
					"customRangeLabel": "Свой",
					"daysOfWeek": [
						"Вс",
						"Пн",
						"Вт",
						"Ср",
						"Чт",
						"Пт",
						"Сб"
					],
					"monthNames": [
						"Январь",
						"Февраль",
						"Март",
						"Апрель",
						"Май",
						"Июнь",
						"Июль",
						"Август",
						"Сентябрь",
						"Октябрь",
						"Ноябрь",
						"Декабрь"
					],
					"firstDay": 1
					}
				});
				$('input[name="to"]').daterangepicker({
					singleDatePicker: true,
					showDropdowns: true,
					"locale": {
					"format": "YYYY-MM-DD",
					"separator": " - ",
					"applyLabel": "Применить",
					"cancelLabel": "Отмена",
					"fromLabel": "От",
					"toLabel": "До",
					"customRangeLabel": "Свой",
					"daysOfWeek": [
						"Вс",
						"Пн",
						"Вт",
						"Ср",
						"Чт",
						"Пт",
						"Сб"
					],
					"monthNames": [
						"Январь",
						"Февраль",
						"Март",
						"Апрель",
						"Май",
						"Июнь",
						"Июль",
						"Август",
						"Сентябрь",
						"Октябрь",
						"Ноябрь",
						"Декабрь"
					],
					"firstDay": 1
					}
				});
			}
		}
		$('#home_widgets').on('click', '#widget_submit', function(event) {
			$('#affiliate_all_widgets').html('<div class="loaded">'+
		'</div>');
			from = $('#home_widgets').find('input[name=from]').val();
			to = $('#home_widgets').find('input[name=to]').val();
			widgets(from, to);
		});
		
		$('.pad_for_widget').change(function(){
			var user_parent=$(this).parents('.user_add_widget');
			if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='1'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="1">Товарный</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='2'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="2">Видео</option>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='3'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="1">Товарный</option>' +
			'<option value="2">Видео</option>' 
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='4'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="3">Тизерный</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='5'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="1">Товарный</option>'+
			'<option value="3">Тизерный</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='6'){
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='8'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="4">Брендирование</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='9'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="1">Товарный</option>'+
			'<option value="4">Брендирование</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='10'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="2">Видео</option>'+
			'<option value="4">Брендирование</option>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='12'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="3">Тизерный</option>'+
			'<option value="4">Брендирование</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
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
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if (user_parent.find($('.pad_for_widget option:selected')).data('type')=='-1'){
			user_parent.find($('.type_for_widget')).css('display', 'block');
			user_parent.find($('.pod_type_for_widget')).html(
			'<option value="1">Товарный</option>'+
			'<option value="2">Видео</option>'+
			'<option value="3">Тизерный</option>'+
			'<option value="4">Брендирование</option>'
			);
			user_parent.find($('.save_for_widget')).html(
			'<a id="submit" class="btn btn-primary">Сохранить</a>'
			);
			}
			else if(user_parent.find($('.pad_for_widget option:selected')).data('type')=='0'){
				user_parent.find($('.type_for_widget')).css('display', 'none');
				user_parent.find($('.pod_type_for_widget')).html('');
				user_parent.find($('.type_for_video')).css('display', 'none');
				user_parent.find($('.save_for_widget')).html(' ');
			}
		});
		
		$('.pod_type_for_widget').change(function(){
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
			}
		});
		
		$('#add_affiliate_widget').on('click', '#submit', function(event) {
			var pad=$('#add_affiliate_widget').find('select[name=pad]').val();
			var type=$('#add_affiliate_widget').find('select[name=type]').val();
			var typeVideo=$('#add_affiliate_widget').find('select[name=typeVideo]').val();
			var id=$('#user_id').html();
			add_widget(id, pad, type, typeVideo);
		});
		
		$('#add_user_dop_status').on('click', '#user_dop_status_submit', function(){
			var user_parent=$(this).parents('.modal-content');
			var id=$(this).data('set');
			var text_for_dop_status=user_parent.find('textarea[name=text_for_dop_status]').val();
			var dop_status=user_parent.find('input:radio[name="dop_status"]:checked').val();
			add_dop_status(id, dop_status, text_for_dop_status);
		});
		
		$('#add_default_on_users').find($('.type_for_commission')).change(function(){
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
				$('#add_default_on_users').on('click', '#default_submit', function(){
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
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#add_default_on_users').on('click', '#destroy_video_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_video_com(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#add_default_on_users').on('click', '#desctroy_product_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_product_com(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#add_default_on_users').on('click', '#desctroy_source_commission', function(){
					var user_parent=$(this).parent()[0];
					var id=$(this).data('set');
					//$(this).css('display', 'none');
					user_parent.style.display='none';
					desctroy_source_com(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#all_users_commission').on('click', '#submit', function(){
				var user_id=$('#all_users_commission').find('input[name=user_id]').val();
				var payment=$('#all_users_commission').find('input[name=payment]').val();
				$('#all_users_commission').find('input[name=payment]').val('0');
				setCommission(user_id, payment);
				$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#all_users_manager').on('click', '#submit', function(){
					var user_id=$('#all_users_manager').find('input[name=user_id]').val();
					var set_manager=$('#all_users_manager').find('select[name=manager]').val();
					setManager(user_id, set_manager);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#affiliate_all_widgets').on('click', '.user_lease', function(){
					var id=$(this).data('set');
					lease(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
				
				$('#affiliate_all_widgets').on('click', '.delete_widget', function(){
					var id=$(this).data('set');
					console.log('123');
					delete_widget(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
						widgets(from, to);
				});
				
				$('#affiliate_all_widgets').on('click', '.user_no_lease', function(){
					var id=$(this).data('set');
					nolease(id);
					$('#affiliate_all_widgets').html('<div class="loaded">'+
						'</div>');
					widgets(from, to);
				});
			});
};

function delete_widget(id){
	$.post('/widget/delete_post/'+id,{ _token: $('meta[name=csrf-token]').attr('content')}, function(response) {
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

function lease(id){
	$.post('/user_lease_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
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

function nolease(id){
	$.post('/user_no_lease_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	id_user: id}, function(response) {
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

function setManager(user_id, set_manager){
	$.post('/user_for_manager_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	manager: set_manager}, function(response) {
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

function setCommission(user_id, payment){
	$.post('/payment_commission_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	payment: payment,}, function(response) {
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

function add_widget(id, pad, type, typeVideo){
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