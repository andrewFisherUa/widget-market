<div class="home_avatar_block">
	@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
		@if (strtotime(date('Y-m-d H:i:s')) < strtotime($user->updated_at)+900)
			<span data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="В сети" class="home_avatar_green"></span>
		@else
			<span data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="Был в сети {{$user->updated_at}}" class="home_avatar_red"></span>
		@endif
	@endif
	@if ($user->profile->vip==1)
		<img src="/images/cabinet/vip.png" style="width: 30px; position: absolute; top: -22px; left: 26px; cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="VIP клиент">
	@endif
	@if ($user->profile->lease==1)
		<span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="На аренде" style="position: absolute; right: 0px; cursor: pointer; color: #ffffff; font-weight: bold;     text-shadow: 0 0 10px #0000ff, 0 0 15px #0000ff, 0 0 20px #0000ff, 0 0 25px #0000ff, 0 0 30px #0000ff, 0 0 35px #0000ff, 0 0 40px #0000ff, 0 0 45px #0000ff; margin-left: 5px;">А</span>
	@endif
	@if ($user->profile->avatar)
		<img src="/images/avatars/{{$user->profile->avatar}}" class="img-circle cabinet_avatar">
	@else
		<img src="/images/cabinet/no_foto.png" class="img-circle cabinet_avatar">
	@endif
</div>
<div class="affiliate_name">{{$user->name}}</div>
<div class="affiliate_role">@if ($user->hasRole('affiliate'))Вебмастер@elseif ($user->hasRole('advertiser'))Рекламодатель@elseif($user->hasRole('manager'))Менеджер@elseif($user->hasRole('super_manager'))Ст. менеджер@elseif($user->hasRole('admin'))Администратор@endif</div>
<div class="affiliate_email">{{$user->email}}</div>
<div class="cabinet_gliph">
	@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
		<a href="{{ route('admin.profile.personal', ['id_user'=>$user->id]) }}" data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="Профиль" target="_blank"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
	@else
		<a href="{{ route('profile.personal') }}" data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="Профиль" target="_blank"><span class="glyphicon glyphicon-user gliph_affiliate"></span></a>
	@endif
	<a target="_blank" href="{{ route('news.all') }}" data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) title="Не прочитаных новостей: {{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}" @else title="Новости" @endif class="home_news"><span class="glyphicon glyphicon-envelope gliph_affiliate"></span>@if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))) @if(count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))>9)<span class="count_news">9+</span> @else <span class="count_news">{{count($user->unreadNotifications->where('type', "App\Notifications\NewNews"))}}</span> @endif @endif</a>
	<a href="{{ route('logout') }}" data-toggle="tooltip"  data-trigger="hover" data-placement="bottom" title="Выйти" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out gliph_affiliate"></span></a>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
		{{ csrf_field() }}
	</form>
</div>