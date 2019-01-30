function pads(){
	$.post('/home_pads',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html() }, function(response) {
		$('#home_pads').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		var script = document.createElement('script');
		script.src = "/js/custom_scroll/jquery.custom-scroll.min.js"
		document.body.appendChild(script);
		script.onload = function() {
			$('#affiliate_all_pads').customScroll({
				offsetTop: 32,
				offsetRight: 16,
				offsetBottom: -32,
				vertical: true,
				horizontal: false
			});
		}
		$('#home_pads').on('click', '#add_submit', function(event) {
			var user_id=$('#user_id').html();
			var domain=$('#add_affiliate_domain').find('input[name=domain]').val();
			var types=$('#add_affiliate_domain').find($(":checkbox:checked"));
			var type=[];
			if (types.length>0){
				for (var i=0; i<types.length; i++){
					type[i]=types[i].value;
				}
			}
			var stcurl=$('#add_affiliate_domain').find('input[name=stcurl]').val();
			var stclogin=$('#add_affiliate_domain').find('input[name=stclogin]').val();
			var stcpassword=$('#add_affiliate_domain').find('input[name=stcpassword]').val();
			if (stcurl=="" || domain==""){
				$('#pad_zap1').css('display', 'block');
				$('#pad_zap2').css('display', 'block');
				return;
			}
			add_pads(user_id, domain, type, stcurl, stclogin, stcpassword);
		});
		$('#home_pads').on('click', '#edit_submit', function(event) {
			var id=$(this).data('set');
			var stcurl=$('#edit_affiliate_domain_'+id).find('input[name=stcurl]').val();
			var stclogin=$('#edit_affiliate_domain_'+id).find('input[name=stclogin]').val();
			var stcpassword=$('#edit_affiliate_domain_'+id).find('input[name=stcpassword]').val();
			if (stcurl==""){
				$('#edit_affiliate_domain_'+id).find($('#pad_zap1')).css('display', 'block');
				return;
			}
			edit_pad(id, stcurl, stclogin, stcpassword);
		});
	});
};

function add_pads(user_id, domain, type, stcurl, stclogin, stcpassword){
	$('#home_pads').html('<div class="loaded">'+
		'</div>');
	$.post('/add_pads_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	domain: domain,
	type: type,
	stcurl: stcurl,
	stclogin: stclogin,
	stcpassword: stcpassword}, function(response) {
		$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
		console.log(response);
		if (response.ok){
			pads();
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

function edit_pad(id, stcurl, stclogin, stcpassword){
	$('#home_pads').html('<div class="loaded">'+
		'</div>');
	$.post('/edit_pad_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	pad_id: id,
	stcurl: stcurl, 
	stclogin: stclogin, 
	stcpassword: stcpassword}, function(response) {
		$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
		console.log(response);
		if (response.ok){
			pads();
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