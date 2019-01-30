<html>
<head>
<title>Market-Place.su</title>
</head>
<body>
<html>
<head>
<meta http-equiv="content-type" content="text/html; ">
</head>
<body text="#000000" bgcolor="#ececec">
	<div style="width: 600px; margin: 0 auto;">
	<a href="http://partner.market-place.su/" target="_blank" style="-webkit-text-size-adjust:none;text-decoration:none!important;line-height:0;">
		<img src="http://widget.market-place.su/images/mp_logo_first.png" alt="Market-Place" style="display:block;" height="49" width="172" border="0">
	</a>
	</div>
	<div style="width: 600px; margin: 0 auto; background: #ffffff;">
		<div style="font-size: 20px; color: #222222; text-align: center; padding: 10px;
		    @if ($news->important==1) 
			border-color: rgba(0, 142, 5, 0.3);
			background-color: rgba(0, 142, 5, 0.5);
			@elseif ($news->important==2)
			border-color: rgba(228, 130, 43, 0.3);
			background-color: rgba(228, 130, 43, 0.5);
			@elseif ($news->important==3)
			border-color: rgba(181, 0, 0, 0.3);
			background-color: rgba(181, 0, 0, 0.5);
			@endif
		">{{$news->header}}</div>
		<div style="padding: 10px;">
		{!!$news->body!!}
		</div>
		<hr style="border-color: #190b0b; border-bottom: 0px; margin: 2px 10px; height: 0px;">
		<table style="width: 100%; padding: 10px;">
			<tr>
				<td style="width: 50%;"><p style="font-size: 14px; color: #222222; padding: 0; margin: 0;">E-mail: support@market-place.su</p>
				<p style="font-size: 14px;  color: #222222; padding: 0; margin: 0;">Skype: manager_alex1</p></td>
				<td style="width: 50%; text-align: right">
					<a style="width: 10px; height: 24px; display: inline-block;" href="https://www.facebook.com/groups/pmplace/" target="_blank">
						<img src="https://widget.market-place.su/images/footer/imgpsh_fullsize(5).png">
					</a>
					<a style="width: 24px; height: 24px; display: inline-block;" href="https://vk.com/cpa_marketplace" target="_blank">
						<img src="https://widget.market-place.su/images/footer/imgpsh_fullsize(7).png">
					</a>
					<a style="width: 24px; height: 24px; display: inline-block;" href="http://searchengines.guru/showthread.php?p=14554541#post14554541" target="_blank">
						<img src="https://widget.market-place.su/images/footer/searchengines_hover.png">
					</a>
					<a style="width: 24px; height: 24px; display: inline-block;" href="http://kote.ws/showthread.php?t=23572&amp;highlight=market" target="_blank">
						<img src="https://widget.market-place.su/images/footer/kote.ws_hover.png">
					</a>
					<a style="width: 24px; height: 24px; display: inline-block;" href="http://www.maultalk.com/topic198141.html" target="_blank">
						<img src="https://widget.market-place.su/images/footer/maultalk_hover.png"></span>
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center; font-size: 14px; color: #222222;">© Market Place, 2015 - 2017</td>
			</tr>
		</table>
    </div>
    <br>
</body>
</html>

</body>
</html>