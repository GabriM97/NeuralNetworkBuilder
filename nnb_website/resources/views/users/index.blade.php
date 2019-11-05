{{-- ADMIN ONLY --}}

@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="container-fluid col-md-11 text-center">
        <a href="{{route("users.create")}}">
            <button class="btn btn-info"><i class="fas fa-user-plus mr-2"></i>CREATE NEW USER</button>
        </a>

        <h2 class="text-left mb-3 mt-3 text-left">Users</h2>
        
        <div class="main-container rounded p-1 my-2">
            <div class="row text-center font-weight-bold">    <!-- TITLE ROW -->
                <div class="col-md-1 align-self-center">ID</div>
                <div class="col-md-1 align-self-center">Username</div>
                <div class="col-md-2 align-self-center">Email</div>
                <div class="col-md-1 align-self-center">Account Type</div>
                <div class="col-md-1 align-self-center">Available Space</div>
                <div class="col-md-1 align-self-center">Models</div>
                <div class="col-md-1 align-self-center">Datasets</div>
                <div class="col-md-1 align-self-center">Tainings</div>
                <div class="col-md-1 align-self-center">Last Login</div>
                <div class="col-md-2 align-self-center">Action</div>
            </div>

            @foreach ($users as $usr)
                <div class="row text-center text-break my-3">
                    <div class="col-md-1 align-self-center">{{$usr->id}}</div>
                    <div class="col-md-1 align-self-center">{{$usr->username}}</div>
                    <div class="col-md-2 align-self-center">{{$usr->email}}</div>
                    <div class="col-md-1 align-self-center px-0">    {{-- ACCOUNT TYPE --}}
                        @php
                            switch ($usr->rank){
                                case -1:    // Admin
                                    $color = "bg-danger";
                                    break;
                                case 0:    // Base user
                                    $color = "bg-secondary";
                                    break;
                                case 1:    // Advanced user
                                    $color = "bg-primary";
                                    break;
                                case 2:    // Professional user
                                    $color = "bg-success text-dark";
                                    break;
                                default:
                                    $color = "";
                                    break;
                            }
                        @endphp
                        <span class="{{$color}} font-weight-bold p-1">{{ ucfirst($usr->getRank()) }}</span>
                    </div>
                    <div class="col-md-1 align-self-center pr-2 pl-2">    {{-- AVAILABLE SPACE --}}
                        @php
                            if($usr->available_space/1024 < 1000) 
                                echo round($usr->available_space/1024, 2)." KB";
                            elseif($usr->available_space/1048576 < 1000) 
                                echo round($usr->available_space/1048576, 2)." MB";
                            else //if($usr->available_space/1073741824 < 1000) 
                                echo round($usr->available_space/1073741824, 2)." GB";
                        @endphp
                        <span class="available-space-bar"></span>
                    </div>
                    <div class="col-md-1 align-self-center">{{$usr->models_number}}</div>
                    <div class="col-md-1 align-self-center">{{$usr->datasets_number}}</div>
                    <div class="col-md-1 align-self-center"> {{$usr->getTrainingNumbers()}} </div>
                    <div class="col-md-1 align-self-center pl-1 pr-1">{{$usr->last_signed_on}}</div>
                    <div class="col-md-2 align-self-center">
                        {{-- DETAILS BUTTON --}}         
                        <a href="{{ route('users.show', ['user' => $usr]) }}" class="text-decoration-none" title="Details">
                            <button class="btn btn-primary btn-circle"><i class="fas fa-list-ul"></i></button>
                        </a>

                        {{-- EDIT BUTTON --}}         
                    <a href="{{ route('users.edit', ['user' => $usr]) }}" class="text-decoration-none" title="Edit">
                        <button class="btn btn-light btn-circle"><i class="fas fa-pen"></i></button>
                    </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
