@extends('master/main')

@section('css_block')
@stop

@section('js_block')
@stop

@section("content")

<div class="container login-page">

    <div class="row">
      <h3 class="col-md-12 text-center">{{ Lang::get('form.login.name') }}</h3>
    </div>

    @if($errors->count())
      <div class="alert alert-danger">w
        @foreach ($errors->all() as $error)
          {{ $error }}<br>
        @endforeach
      </div>
    @endif

    @if(session('login_fail_message'))
      <div class="alert alert-danger">
        {{ session('login_fail_message') }}
      </div>
    @endif

    <div class="row">
      <hr class="col-md-offset-3 col-md-6">
    </div>

    <form class="form-horizontal col-md-offset-3" method="POST" action="{{ asset('/login') }}">
      <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
      <div class="form-group">
        <label for="userName" class="col-md-2 control-label">{{ Lang::get('form.login.account') }}</label>
        <div class="col-md-5">
          <input type="text" class="form-control" id="username" name="username" placeholder="User Name" value="{{ old('username') }}">
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-2 control-label">{{ Lang::get('form.login.password') }}</label>
        <div class="col-md-5">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-md-5">
          <input type="submit" class="btn btn-default btn-block btn-lg" value="{{ Lang::get('form.btn.signin') }}">
        </div>
      </div>
    </form>

    <div class="row">
      <hr class="col-md-offset-3 col-md-6">
    </div>
  </div>
@stop