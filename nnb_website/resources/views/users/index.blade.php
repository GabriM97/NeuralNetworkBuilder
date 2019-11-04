{{-- ADMIN ONLY --}}

@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="container text-center">
        <a href="{{route("users.create")}}">
            <button class="btn btn-info"><strong>+</strong> Create new User</button>
        </a>
    </div>


    <div class="container text-center">
        <h2 class="text-left mb-3 mt-3 text-left">Users</h2>
        
        <div class="container p-0 my-2">
            <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
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
                <div class="row border border-secondary text-center text-break">
                    <div class="col-md-1 align-self-center">{{$usr->id}}</div>
                    <div class="col-md-1 align-self-center">{{$usr->username}}</div>
                    <div class="col-md-2 align-self-center">{{$usr->email}}</div>
                    <div class="col-md-1 align-self-center">    {{-- ACCOUNT TYPE --}}
                        @php
                            switch ($usr->rank){
                                case -1:    // Admin
                                    echo "<span style='color: rgb(200,0,0)'>Admin</span>";
                                    break;
                                case 0:    // Base user
                                    echo "<span style='color: rgb(100,100,100)'>Base</span>";
                                    break;
                                case 1:    // Advanced user
                                    echo "<span style='color: rgb(10,10,200)'>Advanced</span>";
                                    break;
                                case 2:    // Professional user
                                    echo "<span style='color: rgb(10,150,10)'>Professional</span>";
                                    break;
                                default:
                                    echo "Not defined";
                                    break;
                            }
                        @endphp
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
                        <a href="{{ route('users.show', ['user' => $usr]) }}">
                            <button class="btn btn-primary">Details</button>
                        </a>
                    </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
