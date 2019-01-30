@push('cabinet_home_top')
	<style>
		.treeHTML { /* вся форма */
		line-height: normal;
		}
		.treeHTML label { /* пункты и соединяющие их линии */
			position: relative;
			display: block;
			padding: 0 0 0 1.2em;
		}
		.treeHTML label:not(:nth-last-of-type(1)) {
			border-left: 1px solid #94a5bd;
		}
		.treeHTML label:before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 1.1em;
			height: .5em;
			border-bottom: 1px solid #94a5bd;
		}
		.treeHTML label:nth-last-of-type(1):before {
			border-left: 1px solid #94a5bd;
		}
		.treeHTML fieldset, .treeHTML fieldset[class=""] .razvernut { /* списки */ 
			position: absolute;
			visibility: hidden;
			margin: 0;
			padding: 0 0 0 2em;
			border: none;
			display: none;
		}
		.treeHTML fieldset:not(:last-child) {
			border-left: 1px solid #94a5bd;
		}
		.treeHTML .razvernut {
			position: relative;
			visibility: visible;
			display: block;
		}
		.treeHTML > fieldset > legend, .treeHTML .razvernut > fieldset > legend { /* плюс */
			position: absolute;
			left: -5px;
			top: -3px;
			height: 10px;
			width: 10px;
			margin-top: -1em;
			padding: 0;
			border: 1px solid #94a5bd;
			border-radius: 2px;
			background-repeat: no-repeat;
			background-position: 50% 50%;
			background-color: #fff;
			background-image: linear-gradient(to left, #1b4964, #1b4964), linear-gradient(#1b4964, #1b4964), linear-gradient(315deg, #a0b6d8, #e8f3ff 60%, #fff 60%);
			background-size: 1px 5px, 5px 1px, 100% 100%;
			visibility: visible;
			cursor: pointer;
		}
		.treeHTML fieldset[class=""] .razvernut fieldset legend {
			visibility: hidden;
		}
		.treeHTML .razvernut > legend { /* минус */
			background-image: linear-gradient(#1b4964, #1b4964) !important;
			background-size: 5px 1px !important;
		}
</style>
@endpush
<div id="Tree1" class="treeHTML razvernut">
	
	@foreach($ipgs[0] as $gp)
		<label style="margin-bottom: 0px;"><input style="margin-top: 0;" 
		@if ($gp->id==3) data-geo='1' @endif name="vkl[{{$gp->id}}]" type="checkbox" @if(isset($rates[$gp->id])) checked @endif
		>{{$gp->name}}</label>
			
			@if(isset($ipgs[$gp->id]))
				<fieldset style="visibility: visible; display: block; position: relative;">
					<legend style="margin-bottom: 0px;"></legend>
					<fieldset style="width: 500px; margin-left: -33px; margin-top: -8px;">
					@foreach($ipgs[$gp->id] as $vp)
						<label style="margin-bottom: 0px;"><input style="margin-top: 0;" @if(isset($rates[$vp->id])) checked @endif @if ($gp->id==3) data-geo='2' @endif name="vkl[{{$vp->id}}]" type="checkbox"
						>{{$vp->name}}</label>
					@endforeach
					</fieldset>
				</fieldset>
				
			@endif
		
	@endforeach
</div>
@push ('cabinet_home_js')
	<script>
		var t = document.getElementById('Tree1');
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
		});
</script>
@endpush