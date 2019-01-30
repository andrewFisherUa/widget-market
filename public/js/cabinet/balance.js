function balance(){
	$.post('/home_balance',{ _token: $('meta[name=csrf-token]').attr('content'), id_user: $('#user_id').html() }, function(response) {
		$('#home_balance').html(response.view);
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		$('#payment').on('click', '#user_urgently_pay', function(event) {
            if ($('#user_urgently_pay').prop('checked')){
				$('#text_for_user_pay').html('<strong style="color: rgb(181, 0, 0);">Минимальная сумма выплаты 1000 руб.</strong>');
				$('#payment').find('input[name=summa]').prop('min', '1000');
			}
			else{
				$('#text_for_user_pay').html('<strong>Минимальная сумма выплаты 300 руб.</strong>');
				$('#payment').find('input[name=summa]').prop('min', '300');
			}
        });
		$('#payment').on('click', '#payment_submit', function(event) {
			if (+$('#payment').find('input[name=summa]').val() < +$('#payment').find('input[name=summa]').prop('min')){
				console.log('сумма меньше');
				return;
			}
			if (+$('#payment').find('input[name=summa]').val() > +$('#payment').find('input[name=summa]').prop('max')){
				console.log('сумма больше');
				return;
			}
			var sum=$('#payment').find('input[name=summa]').val();
			var payo=$('#payment').find('select[name=pay_option]').val();
			if ($('#payment').find('input[name=urgently]').is(":checked")){
				var urgently=1;
			}
			else{
				var urgently=0;
			}
			payment(sum, payo, urgently);
		});
		
		$('#auto_payment').on('click', '#auto_payment_submit', function(event) {
			event.preventDefault();
			if ($('#auto_payment').find('input[name=urgently]').is(":checked")){
				var urgently=1;
			}
			else{
				var urgently=0;
			}
			if ($('#auto_payment').find('input[name=auto_pay]').is(":checked")){
				var auto_pay=1;
			}
			else{
				var auto_pay=0;
			}
			var user_id=$('#user_id').html();
			var day=$('#auto_payment').find('select[name=day]').val();
			var pay_option=$('#auto_payment').find('select[name=pay_option]').val();
			auto_payment(urgently, auto_pay, user_id, day, pay_option);
		});
		
	});
};

function payment(sum, payo, urgently){
	$('#home_balance').html('<div class="loaded">'+
		'</div>');
	$.post('/add_payout_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: $('#user_id').html(),
	summa: sum,
	urgently: urgently,
	pay_option: payo}, function(response) {
		$('[data-toggle="popover"]').popover({html:true});
		$('[data-toggle="tooltip"]').tooltip();
		$(".modal").modal("hide");
		$('body').removeClass('modal-open');
		$('.modal-backdrop').remove();
		if (response.ok==true){
			balance();
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

function auto_payment(urgently, auto_pay, user_id, day, pay_option){
	$.post('/add_payout_auto_js',{ _token: $('meta[name=csrf-token]').attr('content'), 
	user_id: user_id,
	urgently: urgently,
	auto_pay: auto_pay,
	day: day,
	pay_option: pay_option}, function(response) {
		$(".modal").modal("hide");
		$('body').removeClass('modal-open'); 
		$('.modal-backdrop').remove();
			if (response.ok){
			balance();
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