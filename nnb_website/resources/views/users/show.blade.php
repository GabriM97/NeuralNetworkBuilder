@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="container">
        <div class="row">
            <div class="col h5">
                @if ((Auth::user()->rank == -1))
                    <a class="text-decoration-none rounded text-white p-2" href="{{route("users.index")}}">
                        <i class="fas fa-arrow-circle-left mr-2"></i>Users
                    </a>
                @else
                    <a class="text-decoration-none rounded text-white p-2" href="{{route("home")}}">
                        <i class="fas fa-arrow-circle-left mr-2"></i>Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(isset($return_status))
        @php
            if($return_status == 0) $msg_class = "alert-success";
            else $msg_class = "alert-danger";
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$return_msg}}</div>
        
    @endif

    <div class="main-container rounded container col-md-6 p-2 my-4">
        <h2 class="content-title align-self-center my-3 text-center"><i class="fas fa-user fa-xs mr-2"></i>Profile details</h2>
        
        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Username</div>
            <div class="col-md-6 text-break align-self-center">{{ $user->username }}</div>
        </div>
        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Account type</div>
            <div class="col-md-6 align-self-center">
                @php
                    switch ($user->rank){
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
                <span class="{{$color}} font-weight-bold p-1">{{ ucfirst($user->getRank()) }}</span>
            </div>
        </div>
            
        @if ((Auth::user()->id == $user->id) || (Auth::user()->rank == -1))
            {{-- logged user can visualize its FULL details  --}}

            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right text-break align-self-center font-weight-bold">Email</div>
                <div class="col-md-6 align-self-center">{{ $user->email }} <span>{{ $user->email_verified_at ? "(Verified)" : "(Not verified)" }}</span></div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold"><a href="{{route("datasets.index", compact("user"))}}">Datasets</a></div>
                <div class="col-md-6 align-self-center">{{ $user->datasets_number }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold"><a href="{{route("networks.index", compact("user"))}}">Models</a></div>
                <div class="col-md-6 align-self-center">{{ $user->models_number }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold"><a href="{{route("trainings.index", compact("user"))}}">Trainings</a></div>
                <div class="col-md-6 align-self-center">{{ $user->getTrainingNumbers() }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold">Available space</div>
                <div class="col-md-6 align-self-center"> 
                    @php
                        if($user->available_space/1024 < 1000) 
                            echo round($user->available_space/1024, 2)." KB";
                        elseif($user->available_space/1048576 < 1000) 
                            echo round($user->available_space/1048576, 2)." MB";
                        else //if($user->available_space/1073741824 < 1000) 
                            echo round($user->available_space/1073741824, 2)." GB";
                    @endphp
                    <span class="available-space-bar"></span>
                </div>
            </div> 
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold">Last login</div>
                <div class="col-md-6">{{ $user->last_signed_on }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right align-self-center font-weight-bold">Account created on</div>
                <div class="col-md-6 align-self-center">{{ $user->created_at }}</div>
            </div>
    
            {{-- Edit button --}}
            <div class="row @if(Auth::user()->rank != -1) text-center @else text-right @endif px-5 my-2">
                <div class="col">
                    <a href="{{ route('users.edit', ['user' => $user]) }}">
                        <button class="btn btn-light"><i class="fas fa-pen fa-lg mr-2"></i>Edit</button>
                    </a>
                </div>
                @if (Auth::user()->rank == -1)
                    {{-- Delete button - ADMIN ONLY --}}
                    <div class="col text-left">
                        <form class="form-delete d-inline-block" method="POST" action="{{route("users.destroy", ["user" => $user])}}">
                            @csrf
                            @method("DELETE")
                            <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt fa-lg mr-2"></i>Delete</button>
                        </form>
                    </div>
                @endif
            </div>

            @if (Auth::user()->rank == -1)
                <div class="row my-2">
                    @if ($user->rank >= 0 && $user->rank < 2)
                        {{-- Upgrade button - ADMIN ONLY --}}
                        <div class="col @if($user->rank <= 0) text-center @else text-right @endif">
                            <form class="form-upgrade d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                @csrf
                                @method("PATCH")
                                <input type="hidden" name="process" value="upgradeaccount">
                                <button class='btn btn-success text-dark' type="submit"><i class="fas fa-angle-double-up fa-lg mr-2"></i>Upgrade</button>
                            </form>
                        </div>
                    @endif

                    @if ($user->rank > 0 && $user->rank <= 2)
                        {{-- Downgrade button - ADMIN ONLY --}}
                        <div class="col @if($user->rank >= 2) text-center @else text-left @endif">
                            <form class="form-downgrade d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                @csrf
                                @method("PATCH")
                                <input type="hidden" name="process" value="downgradeaccount">
                                <button class='btn btn-warning' type="submit">Downgrade<i class="fas fa-angle-double-down fa-lg ml-2"></i></button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="row text-center my-2">
                    @if ($user->rank != -1)
                        {{-- Make Admin button - ADMIN ONLY --}}
                        <div class="col">
                            <form class="form-makeadmin d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                @csrf
                                @method("PATCH")
                                <input type="hidden" name="process" value="makeadmin">
                                <button class='btn btn-danger btn-sm' type="submit">
                                    <i class="fas fa-exclamation-triangle fa-lg mr-2 text-warning"></i>MAKE ADMIN<i class="fas fa-angle-double-up fa-lg ml-2"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        @if ($user->id != Auth::user()->id)
                            {{-- Remove Admin button - ADMIN ONLY --}}
                            <div class="col">
                                <form class="form-removeadmin d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                    @csrf
                                    @method("PATCH")
                                    <input type="hidden" name="process" value="removeadmin">
                                    <button class='btn btn-danger btn-sm' type="submit">
                                        <i class="fas fa-exclamation-triangle fa-lg mr-2 text-warning"></i>REMOVE ADMIN<i class="fas fa-angle-double-down fa-lg ml-2"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        @else
            {{-- logged users can visualize NON-SENSITIVE details of user in URL  --}}
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right">Models</div>
                <div class="col-md-6">{{ $user->models_number }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right">Datasets</div>
                <div class="col-md-6">{{ $user->datasets_number }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right">Last login</div>
                <div class="col-md-6">{{ $user->last_signed_on }}</div>
            </div>
            <div class="row my-2 px-5">
                <div class="col-md-6 text-md-right">Account created on</div>
                <div class="col-md-6">{{ $user->created_at }}</div>
            </div>
        @endif
    </div>
@endsection



