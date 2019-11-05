<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield("page-title", config('app.name')) </title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://kit.fontawesome.com/cd5aa7a425.js" crossorigin="anonymous"></script>
    @yield("scripts")

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield("styles")
    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'NeuralNetworkBuilder') }}
                </a>
                <button class="navbar-toggler mb-1" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold {{ Route::is('home') ? 'active-tab' : null }}" href="{{ route('home') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="nav-item">
                                    <a class="nav-link font-weight-bold {{ Route::currentRouteNamed('datasets*') ? 'active-tab' : null }}" href="{{ route('datasets.index', ["user" => Auth::user()]) }}">{{ __('Datasets') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold {{ Route::currentRouteNamed('networks*') ? 'active-tab' : null }}" href="{{ route('networks.index', ["user" => Auth::user()]) }}">{{ __('Models') }}</a>
                            </li>
                            <li class="nav-item">
                                    <a class="nav-link font-weight-bold {{ Route::currentRouteNamed('trainings*') ? 'active-tab' : null }}" href="{{ route('trainings.index', ["user" => Auth::user()]) }}">{{ __('Trainings') }}</a>
                            </li>

                            @if (Auth::user()->rank == -1)
                                <li class="nav-item dropdown mx-3">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle bg-danger text-white font-weight-bold py-0 mt-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Admin panel <span class="caret"></span>
                                    </a>
    
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('users.index') }}">{{ __('All Users') }}</a>
                                        <a class="dropdown-item" href="{{ route('users.create') }}">{{ __('Create user') }}</a>
                                    </div>
                                </li>
                            @endif

                            <li class="nav-item">
                                @php
                                    $size = Auth::user()->get_tot_files_size();
                                    if($size/1024 < 1000) 
                                        $size_render = round($size/1024, 2)." KB ";
                                    elseif($size/1048576 < 1000) 
                                        $size_render = round($size/1048576, 2)." MB ";
                                    else //if($size/1073741824 < 1000) 
                                        $size_render = round($size/1073741824, 2)." GB ";

                                    $max_space = round(Auth::user()->get_max_available_space()/1073741824, 2)." GB used";
                                @endphp
                                <span class="navbar-text px-2 text-light">{{$size_render}} of {{$max_space}}</span>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">   
                                @if(Auth::user()->available_space <= 0)
                                    <div class="alert alert-danger text-center mr-3" role="alert">
                                        NO SPACE AVAILABLE!
                                    </div>
                                @endif        
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle font-weight-bold" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->username }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users.show', ['user' => Auth::user()]) }}">{{ __('Profile') }}</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="my-4">
            {{--
            <div class="container">
                <a href="{{ URL::previous() }}"><button class="btn btn-secondary ml-5">Go Back</button></a>
            </div>
            --}}
            @yield('content')
        </main>
    </div>
</body>
</html>
