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
            <div class="col-sm">ID</div>
            <div class="col-sm">Username</div>
            <div class="col-sm">Email</div>
            <div class="col-sm">Account Type</div>
            <div class="col-sm">Available Space</div>
            <div class="col-sm">Models</div>
            <div class="col-sm">Datasets</div>
            <div class="col-sm">Tainings</div>
            <div class="col-sm">Last Login</div>
            <div class="col-sm">Action</div>
        </div>

        @foreach ($users as $usr)
            <div class="row border border-secondary text-center">
                <div class="col-sm">{{$usr->id}}</div>
                <div class="col-sm">{{$usr->username}}</div>
                <div class="col-sm">{{$usr->email}}</div>
                <div class="col-sm">    {{-- ACCOUNT TYPE --}}
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
                <div class="col-sm">    {{-- AVAILABLE SPACE --}}
                    @if ($usr->available_space/1048576 >= 1024)
                        {{ $usr->available_space/1073741824 }} GB
                    @else
                        {{ $usr->available_space/1048576 }} MB 
                    @endif
                    <span class="available-space-bar"></span>
                </div>
                <div class="col-sm">{{$usr->models_number}}</div>
                <div class="col-sm">{{$usr->datasets_number}}</div>
                <div class="col-sm"> // </div>
                <div class="col-sm">{{$usr->last_signed_on}}</div>
                <div class="col-sm">
                    <a href="{{ route('users.show', ['user' => $usr]) }}">
                        <button class="btn btn-primary">Details</button>
                    </a>
                </div>
          </div>
        @endforeach
    </div>
@endsection
