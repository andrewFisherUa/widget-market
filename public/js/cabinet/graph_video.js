function graph_video(){
	var frame=document.createElement('iframe');
	frame.width='100%';
	frame.height='100%';
	frame.style.border='none';
	frame.src='https://widget.market-place.su/home_graph_video';
	$('#home_video_graph').html(frame);
		
};