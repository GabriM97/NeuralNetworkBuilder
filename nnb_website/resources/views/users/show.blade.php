@extends("layouts.app")

@section('page-title', $title)

@section('content')

    @isset($return_status)
        @php
            if($return_status == 0) $msg_class = "alert-success";
            else $msg_class = "alert-danger";
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$return_msg}}</div>
        
    @endisset

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
        
        @if ((Auth::user()->id == $user->id) || (Auth::user()->rank == -1))
            {{-- logged user can visualize its FULL details  --}}

            <button class='btn btn-outline-primary btn-sm'>Upgrade</button></p>
            <p>Email: {{ $user->email }} <span>{{ $user->email_verified_at ? "(Verified)" : "(Not verified)" }}</span></p>
            <p>Your Models: {{ $user->models_number }}</p>      {{-- add link to user models --}}
            <p>Your Datasets: {{ $user->datasets_number }}</p>    {{-- add link to user datasets --}}
            <p>Available space: {{ $user->available_space/1048576 }} MB <span class="available-space-bar"></span></p>
            <p>Last login: {{ $user->last_signed_on }}</p>
            <p>Account created on: {{ $user->created_at }}</p>
            
            <!-- Edit button -->
            <a href="{{ route('user.edit', ['user' => Auth::user()]) }}"><button class="btn btn-primary">Edit</button></a>
            
            <!-- Delete button - ADMIN ONLY -->
            @if (Auth::user()->rank == -1)
                <form class="form-delete" method="POST" action="{{ route('user.destroy', ['user' => Auth::user()]) }}">
                    <!-- TO DO: test if works without csrf and method("delete") -->
                    @csrf
                    @method("DELETE")       
                    <button class="btn" type="submit">Delete</button>
                </form>
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



