
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <style>
	{!! $style !!}
	
    </style>
    <script>
	{!! $script !!}
    </script>


</head>

<body style="margin: 0;padding: 0;">

<script>
     var cols=1;
    var rows=2;
window.attributes={cols:cols,rows:rows};
    function exec_widget(data){
        data.attributes.cols=cols;
        data.attributes.rows=rows;
        document.getElementById('widget').innerHTML=tmpl('template',data);
    }
    window.AffiliateID = '1';
    window.BannerID = "1";
    window.Data1 = document.location;
    window.Data2 = '1';
    window.Data3 = '1';
    window.Channel ='1';
    function listener(event) {

//        console.log( "получено: " , event.data );
        if(typeof  event.data=="object"){
            if(typeof event.data.offers!="undefined"){
                exec_widget(event.data)
            }

            //console.log( "получено: " , event.data );
        }
        if(typeof  event.data=="string"){
            var data=JSON.parse(event.data);
            if(typeof data.offers!="undefined"){
                exec_widget(data)
            }

            //console.log( "получено: " , event.data );
        }

    }
    if (window.addEventListener) {
        window.addEventListener("message", listener);
    } else {
        // IE8
        window.attachEvent("onmessage", listener);
    }
</script>
<div id='widget' class="mp-widget-wrap {{$name}}">
</div>
<script id="template" type="text">
{!! $body !!}
</script>

<script>
//    window.parent.postMessage({width:"300",height:"400"},'*');
        
        var size=JSON.parse('{"width":"100%","height":"600px"}');

    

</script>
<style id="style_open_size">
    #widget{
    width: 100%!important;
	max-width: 100%!important;
	height: 100%!important;
	background: {!! $background !!}!important;
	font-family: {!! $mobile_font_family !!}!important;
    }
	#mobile_model{
	background: {!! $background_model !!}!important;
	}
	#mobile_model:hover{
	background: {!! $background_model_hover !!}!important;
	}
	.market-place-footer, #footer-back, #footer-back-2{
		@php
		$arr = explode(", ", $background);
		$arr[3]="1)";
		$foot=implode(", ", $arr);
		@endphp
		background: {!! $foot !!}!important;
	}
</style>
<script charset="utf-8" src="//node.market-place.su/div/build/product.js?v=6"></script>
<script></script>

</body>
</html>