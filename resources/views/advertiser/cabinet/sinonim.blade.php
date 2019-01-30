@extends('layouts.app')

@section('content')
<div class="container">
@if (\Auth::user()->hasRole('admin') or \Auth::user()->hasRole('super_manager') or \Auth::user()->hasRole('manager'))
 <div class="row"><a href="{{route('advertiser.add_admin')}}">Список рекламных компаний</a></div>
 @endif
         <div class="alert alert-info">
			Поисковый индекс синонимов обновиться в течении 2 х минут после изменения
		</div>
	        <div class="row">
            <form method="GET" action="" accept-charset="UTF-8"><div class="row">
                <div class="col-md-8">
				    <div class="form-group">
					    <div class="col-md-12">
					        <input name="name" class="form-control" type="text" value ="{{Request('name')}}">
					    </div>
				    </div>
		         </div> 
				    <div class="col-md-2">
					    <input value="Искать" class="btn btn-primary btn-md" type="submit">
                    </div>
                 </div>
             </form>
			</div> 
			@if($name)
				<div>По запросы <b>{{$name}}</b> ничего не найдено совпадений</div>
			@endif	
			<div class ="row">
		<form method='POST' action ="{{route('advertiser.add_sinonim')}}">	
        	{{ csrf_field() }}
			<div class="form-group">
            <div class="col-md-12">
            <div class="form-group row">
                
                <div class="col-md-3">
                    <input type="text" class="form-control" name="name" placeholder="слово">
                </div>
                
                <div class="col-md-3">
                    <input type="text" class="form-control" name="sinonim" placeholder="синоним">
                </div>
				<div class="col-md-3">
                   	<div class="form-group">
					<button type="submit" class="btn btn-primary">
					Добавить
				</div>
                </div>
            </div>
           </div>
        </div>
		</form>	
		</div>
	<h2>Список синонимов</h2>		
<table class="table table-striped">
    <thead>
      <tr>
	    <th></th>
	    <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
	@foreach($collection as $col)
      <tr @if(!$col->indexed) style="color:#CCC" @endif>
	 
		<td>{{$col->name}}</td>
	    <td>{{$col->sinonim}}</td>	
	    <td>
		 <form method="POST" action="{{route('advertiser.delete_sinonim')}}" accept-charset="UTF-8">
		 {{ csrf_field() }}
		 <input type="hidden" name ="id" value="{{$col->id}}">
		            <div class="form-group">
					<button type="submit" class="btn btn-danger">
					Удалить
				    </div>
		 </form>
		  </td>
      </tr>

	  @endforeach
    </tbody>
  </table>
</div> 
@endsection