@extends('layouts.app')
<!-- Styles -->
<style>
    .content {
        text-align: center;
    }

    .title {
        color: #636b6f;
        font-size: 70px;
        text-transform: uppercase;
        margin-bottom: 30px;
        font-weight: 400;
    }

    .sub-title {
        color: #636b6f;
        margin-top: 20px;
        font-size: 40px;
        font-weight: 400;
    }

    .description{
        color: #636b6f;
        padding: 0 5px;
        font-size: 20px;
        font-weight: 500;
        letter-spacing: .1rem;
        line-height: 1.5rem;
    }

    .description a {
        color: #636b6f;
        padding: 0 2px;
        font-size: 20px;
        text-decoration: none;
        text-transform: uppercase;
    }

    .description a:hover {
        color: #505050;
        text-decoration: none;
    }

    .highlight-text {
        color: #404040;
    }
</style>

@section('page-title', 'Welcome | Neural Network Builder ')

@section('content')
    <div class="content">
        <div class="title">
            <div class="sub-title">Welcome to</div>
            Neural Network Builder
        </div>

        <div class="description">
            With this tool you can create, build and edit your own Neural Network Models. <br>
            You can also train your models with personal datasets into a remote server! <br>
            Or you can just predict a value from a pre-trained model. <br>
            What are you waiting for? Start now, <span class="highlight-text">IT'S FREE!</span> <br>
            @auth
                <br>Go to your <a href="{{ url('/home') }}">Home</a>
            @else
                <br><br>
                If you are registered, <a href="{{ route('login') }}">Sing in</a> <br><br>
                Otherwise create a new account and Join Us! <a href="{{ route('register') }}">Register now</a>
            @endauth
        </div>
    </div>
@endsection
