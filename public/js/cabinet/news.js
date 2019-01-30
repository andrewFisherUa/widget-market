function news(){
	$.post('/home_news',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html() }, function(response) {
		$('#home_news').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		var script = document.createElement('script');
		script.src = "/js/custom_scroll/jquery.custom-scroll.min.js"
		document.body.appendChild(script);
		script.onload = function() {
			$('#cabinet_news').customScroll({
				offsetTop: 32,
				offsetRight: 16,
				offsetBottom: -42,
				vertical: true,
				horizontal: false
			});
		}
	});
};