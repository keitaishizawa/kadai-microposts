@if (Auth::user()->is_favorites($micropost->id))
    <!-- 既にお気に入り登録されていた場合 ： 削除する-->
    {!! Form::open(['route' => ['user.delfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('お気に入りから外す', ['class' => "btn btn-danger btn-xs"]) !!}
    {!! Form::close() !!}
@else
    <!-- お気に入り登録されていない場合 ： 登録する-->
    {!! Form::open(['route' => ['user.setfavorite', $micropost->id]]) !!}
        {!! Form::submit('お気に入りに追加', ['class' => "btn btn-primary btn-xs"]) !!}
    {!! Form::close() !!}
@endif