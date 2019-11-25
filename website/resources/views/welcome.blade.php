@extends('layouts.app')

@section('page-title', 'Welcome | Neural Network Builder ')

@section('styles')
	<link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div id="welcome-content" class="content mt-5">
        <div class="sub-title">Welcome to</div>
        <div class="title">Neural Network Builder</div>

        <div class="description">
            With this tool you can build your own Neural Network Models. <br>
            You can also train your models with personal datasets into a remote server! <br>
            Also you can just predict values from your models. <br>
            What are you waiting for? Start now, <span class="highlight-text">IT'S FREE!</span> <br>
            @auth
                <br>Go to your <a class="font-weight-bold" href="{{ url('/home') }}"><u>Home</u></a>
            @else
                <br><br>
                If you are registered, <a href="{{ route('login') }}" class="font-weight-bold"><u>Sign in</u></a> <br><br>
                Otherwise create a new account and Join Us! <a href="{{ route('register') }}" class="font-weight-bold"><u>Register now</u></a>
            @endauth
        </div>
    </div>
@endsection
