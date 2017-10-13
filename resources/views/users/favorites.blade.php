@extends('layouts.app')

@section('content')
    @if (Auth::check())
        <div class="row">
            <div class="col-xs-8">
                @if (count($microposts) > 0)
                    @include('microposts.microposts', ['microposts' => $microposts])
                @endif
            </div>
        </div>
    @endif
@endsection