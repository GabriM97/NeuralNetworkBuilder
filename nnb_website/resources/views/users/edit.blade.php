@extends('layouts.app')

@section('page-title', $title)

@section('content')

<div class="container text-center">

    <div class="row mb-4">
        <div class="col">
            <h2 class="content-title d-inline-block">Edit profile | {{ $user->username }}</h2>
        </div>
    </div>

    @if(Auth::user()->rank == -1)
        <!-- DELETE USER FORM (ADMIN ONLY) -->
        {{-- 
            <form class="form-delete d-inline-block" method="POST" action="{{route("users.destroy", ["user" => $user])}}">
                @csrf
                @method("DELETE")
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
            </form>
        --}}
        
        <!-- CHANGE USERNAME FORM (ADMIN ONLY) -->
        <div class="row mb-2 mt-4">
            <div class="col">
                <h4>Change username</h4>
            </div>
        </div>
        <form class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
            @csrf
            @method("PATCH")
            <input type="hidden" name="process" value="changeusername">
            <div class="row mb-2">
                <div class="col pr-0 text-right">Username</div>
                <div class="col text-left"><input type="text" name="username" value="{{ $user->username }}"></div>
            </div>
            <div class="row">
                <div class="col text-center">
                    <button class="btn btn-primary" type="submit">Confirm</button>
                </div>
            </div>
        </form>
    @endif

    <br>
    <!-- CHANGE EMAIL FORM -->
    <div class="row mb-2">
        <div class="col">
            <h4 class="d-inline-block">Change email</h4> <span>(current: {{ $user->email }})</span>
        </div>
    </div>
    <form class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
        @csrf
        @method("PATCH")
        <input type="hidden" name="process" value="changeemail">

        <div class="row mb-2">
            <div class="col text-right"><input type="email" name="new_email" placeholder="Insert new email"></div>
            <div class="col text-left"><input type="email" name="confirm_new_email" placeholder="Confirm new email"></div>
        </div>

        @if(Auth::user()->rank != -1)
            <div class="row mt-3 mb-2">
                <div class="col"><input type="password" name="current_password" placeholder="Insert current password"></div>
            </div>
        @endif

        <div class="row">
            <div class="col"><button class="btn btn-primary" type="submit">Confirm</button></div>
        </div>
    </form>

    <!-- CHANGE PASSWORD FORM -->
    <div class="row mb-2 mt-4">
        <div class="col">
            <h4>Change password</h4>
        </div>
    </div>
    <form class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
        @csrf
        @method("PATCH")
        <input type="hidden" name="process" value="changepassword">

        <div class="row mb-2">
            <div class="col text-right"><input type="password" name="new_password" placeholder="Insert new password"></div>
            <div class="col text-left"><input type="password" name="confirm_new_password" placeholder="Confirm new password"></div>
        </div>

        @if(Auth::user()->rank != -1)
            <div class="row mt-3 mb-2">
                <div class="col"><input type="password" name="current_password" placeholder="Insert current password"></div>
            </div>
        @endif

        <div class="row">
            <div class="col"><button class="btn btn-primary" type="submit">Confirm</button></div>
        </div>
    </form>
</div>
@endsection