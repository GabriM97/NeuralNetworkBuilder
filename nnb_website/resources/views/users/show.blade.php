@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="text-center">
        <h2 class="content-title">Profile details</h2>
        <p>Username: {{ $user->username }} 
        @php
            switch ($user->rank){
                case -1:    // Admin
                    echo "<span style='color: rgb(200,0,0,.6)'>(Admin)</span>";
                    break;
                case 0:    // Base user
                    echo "<span style='color: rgb(100,100,100,.6)'>(Base)</span>";
                    break;
                case 1:    // Advanced user
                    echo "<span style='color: rgb(10,10,200,.6)'>(Advanced)</span>";
                    break;
                case 2:    // Professional user
                    echo "<span style='color: rgb(10,150,10,.6)'>(Professional)</span>";
                    break;
                default:
                    break;
            } 
        @endphp
        </p>
        
        @if ((Auth::user()->id == $user->id) || (Auth::user()->rank == -1))
            {{-- logged user can visualize its FULL details  --}}

            <p>Email: {{ $user->email }} <span>{{ $user->email_verified_at ? "(Verified)" : "(Not verified)" }}</span></p>
            <p>Available space: {{ $user->available_space/1048576 }} MB <span class="available-space-bar"></span></p>
            <p>Your Models: {{ $user->models_number }}</p>      {{-- add link to user models --}}
            <p>Your Datasets: {{ $user->datasets_number }}</p>    {{-- add link to user datasets --}}
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
            {{-- logged user can visualize NON-SENSITIVE details of user in URL  --}}

            <p>Models: {{ $user->models_number }}</p>
            <p>Datasets: {{ $user->datasets_number }}</p>
            <p>Last login: {{ $user->last_signed_on }}</p>
            <p>Account created on: {{ $user->created_at }}</p>

        @endif
    </div>
@endsection



