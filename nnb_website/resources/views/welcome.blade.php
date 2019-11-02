@extends('layouts.app')

@section('page-title', 'Welcome | Neural Network Builder ')

@section('styles')
	<link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="content">
        <div class="title">
            <div class="sub-title">Welcome to</div>
            Neural Network Builder
        </div>

        <div class="description">
            With this tool you can build your own Neural Network Models. <br>
            You can also train your models with personal datasets into a remote server! <br>
            Also you can just predict values from your models. <br>
            What are you waiting for? Start now, <span class="highlight-text">IT'S FREE!</span> <br>
            @auth
                <br>Go to your <a class="font-weight-bold" href="{{ url('/home') }}">Home</a>
            @else
                <br><br>
                If you are registered, <a href="{{ route('login') }}">Sing in</a> <br><br>
                Otherwise create a new account and Join Us! <a href="{{ route('register') }}">Register now</a>
            @endauth
        </div>
    </div>
@endsection
