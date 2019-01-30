function graph_client(){
	var frame=document.createElement('iframe');
	frame.width='100%';
	frame.height='100%';
	frame.style.border='none';
	var id=$('#user_id').html();
	frame.src='https://widget.market-place.su/home_graph_client?id_user='+id;
	$('#home_efficiency').html(frame);
		
};