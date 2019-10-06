@extends("layouts.app")

@section('page-title', $title)

@section('content')

    @if(isset($return_status))
        @php
            if($return_status == 0) $msg_class = "alert-success";
            else $msg_class = "alert-danger";
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$return_msg}}</div>
        
    @endif

    <div class="container col-6">
        <h2 class="content-title text-center">Profile details</h2>
        <div class="row">
            <div class="col-6 text-right align-self-center font-weight-bold">Username</div>
            <div class="col-6 text-break align-self-center">{{ $user->username }}</div>
        </div>
        <div class="row">
            <div class="col-6 text-right align-self-center font-weight-bold">Account type</div>
            <div class="col-6 align-self-center">
            @php
                switch ($user->rank){
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
        </div>
        
        @if ((Auth::user()->id == $user->id) || (Auth::user()->rank == -1))
            {{-- logged user can visualize its FULL details  --}}

            <div class="row">
                <div class="col-6 text-right text-break align-self-center font-weight-bold">Email</div>
                <div class="col-6 align-self-center">{{ $user->email }} <span>{{ $user->email_verified_at ? "(Verified)" : "(Not verified)" }}</span></div>
            </div>
            <div class="row">
                <div class="col-6 text-right align-self-center font-weight-bold"><a href="{{route("networks.index", compact("user"))}}">Models</a></div>
                <div class="col-6 align-self-center">{{ $user->models_number }}</div>
            </div>
            <div class="row">
            <div class="col-6 text-right align-self-center font-weight-bold"><a href="{{route("datasets.index", compact("user"))}}">Datasets</a></div>
                <div class="col-6 align-self-center">{{ $user->datasets_number }}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right align-self-center font-weight-bold">Available space</div>
                <div class="col-6 align-self-center"> 
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
            <div class="row">
                <div class="col-6 text-right align-self-center font-weight-bold">Last login</div>
                <div class="col-6">{{ $user->last_signed_on }}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right align-self-center font-weight-bold">Account created on</div>
                <div class="col-6 align-self-center">{{ $user->created_at }}</div>
            </div>
            
            <!-- Edit button -->
            <div class="row @if(Auth::user()->rank != -1) text-center @else text-right @endif">
                <div class="col">
                    <a href="{{ route('users.edit', ['user' => $user]) }}">
                        <button class="btn btn-primary">Edit</button>
                    </a>
                </div>
            @if (Auth::user()->rank == -1)
                <!-- Delete button - ADMIN ONLY -->
                <div class="col text-left">
                    <form class="form-delete d-inline-block" method="POST" action="{{route("users.destroy", ["user" => $user])}}">
                        @csrf
                        @method("DELETE")
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>  {{-- row edit button --}}

                <div class="row">
                    @if ($user->rank >= 0 && $user->rank < 2)
                        <!-- Upgrade button - ADMIN ONLY-->
                        <div class="col @if($user->rank <= 0) text-center @else text-right @endif">
                            <form class="form-upgrade d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                @csrf
                                @method("PATCH")
                                <input type="hidden" name="process" value="upgradeaccount">
                                <button class='btn btn-success' type="submit">Upgrade</button>
                            </form>
                        </div>
                    @endif

                    @if ($user->rank > 0 && $user->rank <= 2)
                        <!-- Downgrade button - ADMIN ONLY-->
                        <div class="col @if($user->rank >= 2) text-center @else text-left @endif">
                            <form class="form-downgrade d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                                @csrf
                                @method("PATCH")
                                <input type="hidden" name="process" value="downgradeaccount">

                                <button class='btn btn-warning' type="submit">Downgrade</button>
                            </form>
                        </div>
                    @endif
                </div>
            <div class="row text-center">
                @if ($user->rank != -1)
                    <!-- Make Admin button - ADMIN ONLY-->
                    <div class="col">
                        <form class="form-makeadmin d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                            @csrf
                            @method("PATCH")
                            <input type="hidden" name="process" value="makeadmin">
                            <button class='btn btn-outline-danger btn-sm' type="submit">MAKE ADMIN</button>
                        </form>
                    </div>
                @else
                    <!-- Remove Admin button - ADMIN ONLY-->
                    <div class="col">
                        <form class="form-removeadmin d-inline-block" method="POST" action="{{route("users.update", ["user" => $user])}}">
                            @csrf
                            @method("PATCH")
                            <input type="hidden" name="process" value="removeadmin">
                            <button class='btn btn-outline-danger btn-sm' type="submit">REMOVE ADMIN</button>
                        </form>
                    </div>
                @endif
            @endif
            </div>  {{-- tag row edit/admin button closed --}}
        @else
            {{-- logged user can visualize NON-SENSITIVE details of user in URL  --}}

            <div class="row">
                <div class="col-6 text-right">Models</div>
                <div class="col-6">{{ $user->models_number }}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right">Datasets</div>
                <div class="col-6">{{ $user->datasets_number }}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right">Last login</div>
                <div class="col-6">{{ $user->last_signed_on }}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right">Account created on</div>
                <div class="col-6">{{ $user->created_at }}</div>
            </div>
        @endif
    </div>
@endsection



