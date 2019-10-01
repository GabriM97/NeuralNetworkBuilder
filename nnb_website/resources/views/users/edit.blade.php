@extends('layouts.app')

@section('page-title', $title)

@section('content')

<div class="container text-center">
    <h2 style="display:inline-block" class="content-title">Edit profile | {{ $user->username }}</h2>
        
        @if(Auth::user()->rank == -1)
            <!-- DELETE USER FORM (ADMIN ONLY) -->
            {{-- 
                <form style="display:inline-block" class="form-delete" method="POST" action="{{route("user.destroy", ["user" => $user])}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                </form>
            --}}
            <br><br>
            <!-- CHANGE USERNAME FORM (ADMIN ONLY) -->
            <form class="form-edit" method="POST" action="{{route("user.update", ["user" => $user])}}">
                @csrf
                @method("PATCH")
                <input type="hidden" name="process" value="changeusername">

                <p>Username <input type="text" name="username" value="{{ $user->username }}"></p>
                <button type="submit">Confirm</button>
            </form>
        @endif

        <br>
        <!-- CHANGE EMAIL FORM -->
        <h4 style="display: inline-block">Change email</h4> <span>(current: {{ $user->email }})</span>
        <form class="form-edit" method="POST" action="{{route("user.update", ["user" => $user])}}">
            @csrf
            @method("PATCH")
            <input type="hidden" name="process" value="changeemail">

            <p><input type="email" name="new_email" placeholder="Insert new email"></p>
            <p><input type="email" name="confirm_new_email" placeholder="Confirm new email"></p>
            <p><input type="password" name="current_password" placeholder="Insert current password"></p>

            <button type="submit">Confirm</button>
        </form>

        <br>

        <!-- CHANGE PASSWORD FORM -->
        <h4>Change password</h4>
        <form class="form-edit" method="POST" action="{{route("user.update", ["user" => $user])}}">
            @csrf
            @method("PATCH")
            <input type="hidden" name="process" value="changepassword">

            <p><input type="password" name="current_password" placeholder="Insert current password"></p>
            <p><input type="password" name="new_password" placeholder="Insert new password"></p>
            <p><input type="password" name="confirm_new_password" placeholder="Confirm new password"></p>
        
            
            <button type="submit">Confirm</button>
        </form>
    </div>
@endsection