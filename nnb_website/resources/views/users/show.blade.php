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

    <div class="container text-center">
        <h2 class="content-title">Profile details</h2>
        <p>Username: {{ $user->username }}</p>

        <p>Account type:
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
        </p>
        
        @if ((Auth::user()->id == $user->id) || (Auth::user()->rank == -1))
            {{-- logged user can visualize its FULL details  --}}

            <p>Email: {{ $user->email }} <span>{{ $user->email_verified_at ? "(Verified)" : "(Not verified)" }}</span></p>
            <p>Your Models: {{ $user->models_number }}</p>      {{-- add link to user models --}}
            <p>Your Datasets: {{ $user->datasets_number }}</p>    {{-- add link to user datasets --}}
            <p>Available space:
                @if ($user->available_space/1048576 >= 1024)
                    {{ $user->available_space/1073741824 }} GB
                @else
                    {{ $user->available_space/1048576 }} MB 
                @endif
                <span class="available-space-bar"></span>
            </p>
            <p>Last login: {{ $user->last_signed_on }}</p>
            <p>Account created on: {{ $user->created_at }}</p>
            
            <!-- Edit button -->
            <a href="{{ route('user.edit', ['user' => $user]) }}"><button class="btn btn-primary">Edit</button></a>
            
            @if (Auth::user()->rank == -1)
                <!-- Delete button - ADMIN ONLY -->
                <form class="form-delete d-inline-block" method="POST" action="{{route("user.destroy", ["user" => $user])}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>

                @if ($user->rank >= 0 && $user->rank < 2)
                    <!-- Upgrade button - ADMIN ONLY-->
                    <form class="form-upgrade d-inline-block" method="POST" action="{{route("user.update", ["user" => $user])}}">
                        @csrf
                        @method("PATCH")
                        <input type="hidden" name="process" value="upgradeaccount">

                        <button class='btn btn-success' type="submit">Upgrade</button>
                    </form>
                @endif

                @if ($user->rank > 0 && $user->rank <= 2)
                    <!-- Downgrade button - ADMIN ONLY-->
                    <form class="form-downgrade d-inline-block" method="POST" action="{{route("user.update", ["user" => $user])}}">
                        @csrf
                        @method("PATCH")
                        <input type="hidden" name="process" value="downgradeaccount">

                        <button class='btn btn-warning' type="submit">Downgrade</button>
                    </form>
                @endif
                
                <br><br>

                @if ($user->rank != -1)
                    <!-- Make Admin button - ADMIN ONLY-->
                    <form class="form-makeadmin d-inline-block" method="POST" action="{{route("user.update", ["user" => $user])}}">
                        @csrf
                        @method("PATCH")
                        <input type="hidden" name="process" value="makeadmin">

                        <button class='btn btn-outline-danger btn-sm' type="submit">MAKE ADMIN</button>
                    </form>
                @endif

                @if ($user->rank == -1)
                    <!-- Remove Admin button - ADMIN ONLY-->
                    <form class="form-removeadmin d-inline-block" method="POST" action="{{route("user.update", ["user" => $user])}}">
                        @csrf
                        @method("PATCH")
                        <input type="hidden" name="process" value="removeadmin">

                        <button class='btn btn-outline-danger btn-sm' type="submit">REMOVE ADMIN</button>
                    </form>
                @endif

            @endif
        @else
        </p>    <!-- close Account type tag -->
            {{-- logged user can visualize NON-SENSITIVE details of user in URL  --}}

            <p>Models: {{ $user->models_number }}</p>
            <p>Datasets: {{ $user->datasets_number }}</p>
            <p>Last login: {{ $user->last_signed_on }}</p>
            <p>Account created on: {{ $user->created_at }}</p>

        @endif
    </div>
@endsection



