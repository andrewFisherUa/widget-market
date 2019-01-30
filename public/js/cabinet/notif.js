function notif(){
	$.post('/home_notif',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html() }, function(response) {
		$('#home_notif').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		var script = document.createElement('script');
		script.src = "/js/custom_scroll/jquery.custom-scroll.min.js"
		document.body.appendChild(script);
		script.onload = function() {
			$('#notif_accordion').customScroll({
				offsetTop: 32,
				offsetRight: 16,
				offsetBottom: -42,
				vertical: true,
				horizontal: false
			});
			$(document).on('click', '.remove_notif', function(event) {
				var id=$(this).data('set');
					event.preventDefault();
					$.post('/home_remove_norification/'+id,{ _token: $('meta[name=csrf-token]').attr('content')}, function(response) {
					if (response.ok){
						$('#notif_'+id).css('display', 'none');
						}
						else{
							$('#home_notif').html("<div class='no_manager text-center'>Возникла ошибка, пожалуйста обновите страницу.</duiv>");
					}
				});
			});
		}
	});
};