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

<h2 class="ml-5">Users</h2>
    <div class="container text-center">
        <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
            <div class="col-md-1">ID</div>
            <div class="col-md-1">Username</div>
            <div class="col-md-2">Email</div>
            <div class="col-md-1">Account Type</div>
            <div class="col-md-1">Available Space</div>
            <div class="col-md-1">Models</div>
            <div class="col-md-1">Datasets</div>
            <div class="col-md-1">Tainings</div>
            <div class="col-md-2">Last Login</div>
            <div class="col-md-1">Action</div>
        </div>

        @foreach ($users as $usr)
            <div class="row border border-secondary text-center">
                <div class="col-md-1">{{$usr->id}}</div>
                <div class="col-md-1">{{$usr->username}}</div>
                <div class="col-md-2">{{$usr->email}}</div>
                <div class="col-md-1">    {{-- ACCOUNT TYPE --}}
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
                <div class="col-md-1">    {{-- AVAILABLE SPACE --}}
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
                <div class="col-md-1">{{$usr->models_number}}</div>
                <div class="col-md-1">{{$usr->datasets_number}}</div>
                <div class="col-md-1"> // </div>
                <div class="col-md-2">{{$usr->last_signed_on}}</div>
                <div class="col-md-1">
                    <a href="{{ route('users.show', ['user' => $usr]) }}">
                        <button class="btn btn-primary">Details</button>
                    </a>
                </div>
          </div>
        @endforeach
    </div>
@endsection
