
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
     var cols={{ $cols }};
    var rows={{ $row }};
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
        
        var size=JSON.parse('{"width":"{!!$width!!}","height":"{!!$height!!}"}');

    

</script>
<style id="style_open_size">
    #widget{
        
	@if ($width=='0')
		width: 100%!important;
		max-width: 100%!important;
	@else
		width: {!! $width !!}!important;
		max-width: {!! $width !!}!important;
	@endif
	
	@if ($height=='0')
		$height: 100%!important;
	@else
		height: {!! $height !!}!important;
	@endif
	
	@if ($name=='module-block-yandex_left')
		@php
		$arr = explode(", ", $background);
		$arr[3]="1)";
		$foot=implode(", ", $arr);
		@endphp
		background: {!! $foot !!}!important;
	@else
		background: {!! $background !!}!important;
	@endif
		border-radius: {!! $border_radius !!}px!important;
		font-family: {!! $font_family !!}!important;
    }
	.market-place-footer, #footer-back, #footer-back-2{
		@php
		$arr = explode(", ", $background);
		$arr[3]="1)";
		$foot=implode(", ", $arr);
		@endphp
		background: {!! $foot !!}!important;
	}
	#widget_coll{
		border-color: {!! $border_color !!}!important;
		border-width: {!! $border_width !!}px!important;
		border-radius: {!! $border_radius !!}px!important;
	}
	#model_cust{
		background: {!! $background_model !!}!important;
	}
	@if ($name=='table-mini' || $name=='module-block-tetra' || $name='table' || $name='table-no-foto')
		#model_cust:hover{
		background: {!! $background_model_hover !!}!important;
	}
	@endif
	@if ($name=='module-block')
	#model-hover:hover{
		background: {!! $background_model_hover !!}!important;
	}
	@else
	#model-hover{
		background: {!! $background_model_hover !!}!important;
	}
	@endif
	@if ($name=='table-mini' || $name='module-block')
	#font_size3, #font_size1, #font_size2{
		font-size: calc(12px * {!! $font_size !!})!important;
	}
	#font_size4{
		line-height: 1!important;
		font-size: calc(12px * {!! $font_size !!})!important;
	}
	@else
	#font_size3{
		line-height: 1.4!important;
		font-size: calc(12px * {!! $font_size !!})!important;
	}
	#font_size1, #font_size2, #font_size4{
		line-height: 1.4!important;
		font-size: calc(12px * {!! $font_size !!})!important;
	}
	@endif
</style>
<script charset="utf-8" src="//node.market-place.su/div/build/product.js?v=6"></script>
<script></script>

</body>
</html>