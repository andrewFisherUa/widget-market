{{--@extends('layouts.app')

@section('content')--}}
{{--
массив из контроллера
$geos=\DB::table('geo')->whereIn('country_iso', ['AZ', 'AM', 'BY', 'KZ', 'KG', 'MD', 'RU', 'TJ', 'UZ', 'TM', 'UA'])->orderBy('country', 'asc')->orderBy('region', 'asc')->orderBy('city', 'asc')->get();
	$ggg=[];
	foreach ($geos as $geo){
		if ($geo->country_iso){
			if (!isset($ggg[$geo->country_iso]))
				$ggg[$geo->country_iso]=[];
				if (!isset($ggg[$geo->country_iso]['id_geo']))
					$ggg[$geo->country_iso]['id_geo']=$geo->id_geo;
				if (!isset($ggg[$geo->country_iso]['country']))
					$ggg[$geo->country_iso]['country']=$geo->country;
			}
			if ($geo->region_iso){
				if (!isset($ggg[$geo->country_iso][$geo->region_iso]))
					$ggg[$geo->country_iso][$geo->region_iso]=[];
				if (!$geo->city){
					if (!isset($ggg[$geo->country_iso][$geo->region_iso]['id_geo']))
						$ggg[$geo->country_iso][$geo->region_iso]['id_geo']=$geo->id_geo;
					if (!isset($ggg[$geo->country_iso][$geo->region_iso]['region']))
						$ggg[$geo->country_iso][$geo->region_iso]['region']=$geo->region;
				}
			}
			if ($geo->city){
				if (!isset($ggg[$geo->country_iso][$geo->region_iso]['city'][$geo->city])){
					$ggg[$geo->country_iso][$geo->region_iso]['city'][$geo->city]=$geo->id_geo;
				}
			}

--}}
<html>
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
body{
	padding: 0;
	margin: 0;
}
input[type="checkbox" i] {
    margin: 3px 0px 0px 2.5px;
}
.Container {
    padding: 0;
    margin: 0;
}

.Container li {
    list-style-type: none;
}



/* indent for all tree children excepts root */
.Node {
    background-image : url(img/i.gif);
    background-position : top left;
    background-repeat : repeat-y;
    margin-left: 18px;
    zoom: 1;
}

.IsRoot {
    margin-left: 0;
}


/* left vertical line (grid) for all nodes */
.IsLast {
    background-image: url(img/i_half.gif);
    background-repeat : no-repeat;
}
 
.ExpandOpen .Expand {
    background-image: url(img/expand_minus.gif);
}
 
/* closed is higher priority than open */
.ExpandClosed .Expand {
    background-image: url(img/expand_plus.gif);
}
 
/* highest priority */
.ExpandLeaf .Expand {
    background-image: url(img/expand_leaf.gif);
}

.Content {
    min-height: 18px;
    /*margin-left:18px;*/
	display: inline-block
}

* html  .Content {
    height: 18px; 
}

.Expand {
    width: 18px;
    height: 18px;
    float: left;
}


.ExpandLoading   {
    width: 18px;
    height: 18px;
    float: left;
    background-image: url(img/expand_loading.gif);
}



.ExpandOpen .Container {
	display: block;
}

.ExpandClosed .Container {
	display: none;
}

.ExpandOpen .Expand, .ExpandClosed .Expand {
	cursor: pointer;
}
.ExpandLeaf .Expand {
	cursor: auto;
}

</style>
</head>
<body>
<div id="app">
	<div onclick="tree_toggle(arguments[0])">
		<ul class="Container">
			@foreach($geos as $k=>$geo)
			<li class="Node IsRoot ExpandClosed">
				@if(count($geo)>2)
				<div class="Expand"></div>
				@endif
				<input type="checkbox"/>
				<div class="Content">{{$geo['country']}}</div>
				@if(count($geo)>2)
					<ul class="Container region">
						@foreach ($geo as $ge)
							@if (isset($ge['region']))
								<li class="Node ExpandClosed">
									<div class="Expand"></div>
									<input type="checkbox"/>
									<div class="Content">{{$ge['region']}}</div>
									<ul class="Container">
										@foreach ($ge['city'] as $city=>$id)
											<li class="Node ExpandLeaf IsLast">
												<div class="Expand"></div>
												<input type="checkbox"/>
												<div class="Content">{{$city}}</div>
											</li>
										@endforeach
									</ul>
								</li>
							@endif
						@endforeach
					</ul>
				@endif
			</li>
			@endforeach
		</ul>
	</div>
</div>
<script src="./js/app.js"></script>
<script>
	function tree_toggle(event) {
		event = event || window.event
		var clickedElem = event.target || event.srcElement
		if (!hasClass(clickedElem, 'Expand')) {
			return // клик не там
		}
		// Node, на который кликнули
		var node = clickedElem.parentNode
		if (hasClass(node, 'ExpandLeaf')) {
			return // клик на листе
		}
		// определить новый класс для узла
		var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen'
		// заменить текущий класс на newClass
		// регексп находит отдельно стоящий open|close и меняет на newClass
		var re =  /(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/
		node.className = node.className.replace(re, '$1'+newClass+'$3')
	}
	function hasClass(elem, className) {
		return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className)
	}
	$('input[type="checkbox"]').click(function(){
		if ($(this).prop('checked')){
			var children=$(this).parent().find('input[type="checkbox"]');
			if (children.length>1){
				for (var i=0, j=children.length; i<j; i++){
					children[i].checked = true;
				}
			}
			var par=$(this).parents('.Node');
			if (par.length>1){
				for (var i=0, j=par.length; i<j; i++){
					if (par[i].querySelectorAll('input[type="checkbox"]').length - 1==par[i].querySelectorAll('input[type="checkbox"]:checked').length){
						par[i].querySelector('input[type="checkbox"]').checked = true;
					}
				}
			}
		}
		else{
			var par=$(this).parents('.Node');
			if (par.length>1){
				for (var i=0, j=par.length; i<j; i++){
					par[i].querySelector('input[type="checkbox"]').checked = false;
				}
			}
			var children=$(this).parent().find('input[type="checkbox"]');
			if (children.length>1){
				for (var i=0, j=children.length; i<j; i++){
					children[i].checked = false;
				}
			}
		}
	});

	
	
		/*var t = document.getElementById('Tree1');
		[].forEach.call(t.querySelectorAll('fieldset'), function(eFieldset) {
			var main = [].filter.call(t.querySelectorAll('[type="checkbox"]'), function(element) {return element.parentNode.nextElementSibling == eFieldset;});
			main.forEach(function(eMain) {
				var l = [].filter.call(eFieldset.querySelectorAll('legend'), function(e) {return e.parentNode == eFieldset;});
				l.forEach(function(eL) {
					var all = eFieldset.querySelectorAll('[type="checkbox"]');
					eL.onclick = Razvernut;
					eFieldset.onchange = Razvernut;
					function Razvernut() {
						var allChecked = eFieldset.querySelectorAll('[type="checkbox"]:checked').length;
						//eMain.checked = allChecked == all.length;
						eMain.indeterminate = allChecked > 0 && allChecked < all.length;
						if (eMain.indeterminate || (eFieldset.querySelector('fieldset').className == '')) {
							eFieldset.querySelector('fieldset').className = 'razvernut';
							eFieldset.style.height = eFieldset.querySelector('fieldset').offsetHeight+'px';
						} else {
							eFieldset.querySelector('fieldset').className = '';
							eFieldset.style.height = 'auto';
						}
					}
				});
			});
		});*/
		
		/*var t = document.forms.Tree1;
[].forEach.call(t.querySelectorAll('fieldset'), function(eFieldset) {
  var main = [].filter.call(t.querySelectorAll('[type="checkbox"]'), function(element) {return element.parentNode.nextElementSibling == eFieldset;});
  main.forEach(function(eMain) {
    var l = [].filter.call(eFieldset.querySelectorAll('legend'), function(e) {return e.parentNode == eFieldset;});
    l.forEach(function(eL) {
      var all = eFieldset.querySelectorAll('[type="checkbox"]');
      eL.onclick = Razvernut;
      eFieldset.onchange = Razvernut;
      window.addEventListener('load', function() {  // при загрузки страницы установить атрибут checked у тех input, что указаны в параметре URL checked[]
        var urlPar = new URLSearchParams(location.search.slice(1));
        if (urlPar.has('checked[]')) {
          for(var i=0; i<urlPar.getAll('checked[]').length; i++)
            t.querySelector('[type="checkbox"][data-checked="'+urlPar.getAll('checked[]')[i]+'"]').checked = true;
          Razvernut('true');
        }
      });
      function Razvernut(load) {
        if(load == 'true') {  // если атрибут checked есть у input, стоящего выше по лестнице, то установить атрибут checked у как бы вложенных input
          var eCh0 = [].filter.call(t.querySelectorAll('fieldset [type="checkbox"]:not(:checked)'), function(element) {return element.parentNode.parentNode.previousElementSibling.firstElementChild.checked == true}); 
          for(var i=0; i<eCh0.length; i++)
            eCh0[i].checked = true;
        }
        var allChecked = eFieldset.querySelectorAll('[type="checkbox"]:checked').length;
        eMain.checked = allChecked == all.length;
        eMain.indeterminate = allChecked > 0 && allChecked < all.length;
        if (eMain.indeterminate || eMain.checked || ((eFieldset.className == '') && (allChecked == "0") && (load != 'true'))) {
          eFieldset.className = 'razvernut';
        } else {
          eFieldset.className = '';
        }
      }
      eMain.onclick = function() {
        for(var i=0; i<all.length; i++)
          all[i].checked = this.checked;
        if (this.checked) {
          eFieldset.className = 'razvernut';
        } else {
          eFieldset.className = '';
        }
      }
    });
  });
});*/
</script>
</body>
</html>
{{--@endsection--}}
