@push('cabinet_home')
	<style>
	.radio_buttons1 {
    font-size: 14px
	}
	.radio_buttons1 div {
		float: left;
	}
	.radio_buttons1 input {
		position: absolute;
		left: -9999px;
	}
	.radio_buttons1 label {
		display: block;
		margin: 0px;
		padding: 8px 10px;
		border: 1px solid transparent;
		color: #333;
		background-color: #ebebeb;
		border-color: #1f648b;
		cursor: pointer;
	}
	.radio_buttons1 input:checked + label {
		color: #fff;
		background-color: #3097D1;
		border-color: #1f648b;
	}
	.radio_buttons1 div:first-child label {
		margin-left: 0;
		border-top-left-radius: 4px;
		border-bottom-left-radius: 4px;
	}
	.radio_buttons1 div:last-child label {
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="https://widget.market-place.su/js/daterange/moment.js"></script>
	<script src="https://widget.market-place.su/js/daterange/daterangepicker.js"></script>
<script>

		$(function(){
		$('#indebeznds').hide();
		        $('#payments form input[name="name"]').change(function(){
					
                    if($('#payments form input[name="form"]:checked').val() ==1){
						$('#payments form input[name="firm_name"]').val($(this).val()); 
					}
				});
				$('#payments form input[name="form"]').change(function(){
						$('#indebeznds').hide();
					if($(this).val()==1){
						if($('#payments form input[name="nds"]:checked').val() !=1)
						$('#indebeznds').show();
						$('#payments form input[name="nds"]').eq(0).prop( "checked", true );


                    var ip=$('#payments form input[name="name"]').val().split(' ');
					if (ip.length==3){
					var name=$('#payments form input[name="name"]').val();
					}
					else{
					var name="";
					}
					
                    $('#payments form input[name="firm_name"]').val(name);
                    $('#payments form [data-set="firm_name"] label').html('ИП');
                    //$('#payments form [data-set="position"] label').html('Должность лица, заключающего договор');
                    $('#payments form input[name="firm_name"]').prop("readonly", true); 
                    //$('#payments form select[name="position"]').
                    $('#payments form select[name="position"]').empty()
                   .append('<option value="Руководитель">Руководитель</option>'); 


				}else{
                $('#payments form input[name="firm_name"]').val('');
                

                $('#payments form [data-set="firm_name"] label').html('Полное наименование организации');
                //$('#payments form [data-set="position"] label').html('Генеральный директор');
                $('#payments form input[name="firm_name"]').prop("readonly", false); 
                $('#payments form select[name="position"]').empty()
                .append('<option value="Руководитель">Руководитель</option>')
                .append('<option value="Директор">Директор</option>')
                .append('<option value="Генеральный директор">Генеральный директор</option>'); 
                }});
				$('#payments form input[name="nds"]').change(function(){
					$('#indebeznds').hide();
					if($(this).val()==2){
				    if($('#payments form input[name="form"]:checked').val()==1){
						$('#indebeznds').show();
						$('#payments form input[name="nds"]').eq(0).prop( "checked", true );
					}
						//$('#payments form input[name="nds"]').eq(0).prop( "checked", true );
				}});
		
		});
    $('input[name="date_certificate"]').daterangepicker({
	singleDatePicker: true,
	    //autoUpdateInput: false,
		//startDate: '2017-01-02',
		//startDate: 'YYYY-MM-DD',
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
</script>
			<script>
		$('[data-toggle="popover"]').popover({html:true
		});
	</script>
@endpush