@if ($manager!='0')
	<div class="affiliate_manager">Ваш менеджер:</div>
	@if (\App\User::find($user->Profile->manager)->Profile->avatar)
		<div class="home_avatar_block">
			@if (strtotime(date('Y-m-d H:i:s'))<strtotime(\App\User::find($user->Profile->manager)->updated_at)+900)
				<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер online" class="home_avatar_green"></span>
			@else
				<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер offline"  class="home_avatar_red"></span>
			@endif
			<img class="img-circle manager_avatar" src="/images/avatars/{{\App\User::find($user->Profile->manager)->Profile->avatar}}">
		</div>
	@else
		<div class="home_avatar_block">
			@if (strtotime(date('Y-m-d H:i:s'))<strtotime(\App\User::find($user->Profile->manager)->updated_at)+900)
				<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер online" class="home_avatar_green"></span>
			@else
				<span data-toggle="tooltip" data-placement="bottom" title="Ваш менеджер offline"  class="home_avatar_red"></span>
			@endif
			<img class="img-circle manager_avatar" src="/images/cabinet/no_foto.png">
		</div>
	@endif
	<div class="affiliate_name">{{\App\User::find($user->Profile->manager)->Profile->name}}</div>			
	<div class="manager_skype"><b>Skype:</b> <a href='skype:{{\App\User::find($user->Profile->manager)->Profile->skype}}?chat'>{{\App\User::find($user->Profile->manager)->Profile->skype}}</a></div>
	<div class="manager_skype"><b>Email:</b> <a href='mailto:{{\App\User::find($user->Profile->manager)->Profile->email}}'>{{\App\User::find($user->Profile->manager)->Profile->email}}</a></div>
@else
	<div class="no_manager">После добавления площадки за Вами будет закреплен персональный менеджер, если у Вас есть какие либо вопросы, обратитесь в службу поддержки: <b>support@market-place.su</b></div>
@endif