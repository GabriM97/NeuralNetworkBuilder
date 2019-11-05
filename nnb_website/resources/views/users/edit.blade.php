@extends('layouts.app')

@section('page-title', $title)

@section('scripts')
    <script src="{{ asset('js/edit_form_control.js') }}"></script>
@endsection

@section('content')

    <div class="container col-6 text-center">
        <div class="row mb-4">
            <div class="col">
                <h2 class="content-title d-inline-block">Edit profile | {{ $user->username }}</h2>
            </div>
        </div>

        @if(Auth::user()->rank == -1)
            <!-- CHANGE USERNAME FORM (ADMIN ONLY) -->
            <div class="form-group my-5">
                <div class="row my-2">
                    <div class="col">
                        <h4>Change username</h4>
                    </div>
                </div>
                <form class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                    @csrf
                    @method("PATCH")
                    <input type="hidden" name="process" value="changeusername">

                    <div class="row mb-2">
                        <label for="username" class="col-5 col-form-label pr-0 text-right font-weight-bold">Username</label>
                        <div class="col-5 text-left">
                            <input id="username" type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ $user->username }}" required>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        {{-- CONFIRM BUTTON --}}
                        <div class="col text-right px-0">
                            <button class="btn btn-primary" type="submit">Confirm</button>
                        </div>

                        {{-- CANCEL BUTTON --}}
                        <div class="col text-left px-0">
                            <a href="{{ route("users.show", compact("user")) }}">
                                <button type="button" class="btn btn-secondary">Cancel</button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- CHANGE EMAIL FORM -->
        <div class="form-group my-5">    
            <div class="row my-2">
                <div class="col">
                    <h4 class="d-inline-block">Change email</h4> &ensp;<span>(current: {{ $user->email }})</span>
                </div>
            </div>
            <form id="form-email" class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                @csrf
                @method("PATCH")
                <input type="hidden" name="process" value="changeemail">

                <div class="row mb-2">
                    <div class="col-5 offset-1 text-right"> {{-- New email --}}
                        <input type="email" id="new_email" name="new_email" class="form-control @error('new_email') is-invalid @enderror" placeholder="Insert new email" required>
                        @error('new_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-5 text-left"> {{-- Confirm new email --}}
                        <input type="email" id="confirm_new_email" name="confirm_new_email" class="form-control @error('confirm_new_email') is-invalid @enderror" placeholder="Confirm new email" required>
                        @error('confirm_new_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->rank !== -1)
                    <div class="row mt-3 mb-2">
                        <div class="offset-4 col-4">    {{-- Current password --}}
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Insert current password" required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="row">
                    {{-- CONFIRM BUTTON --}}
                    <div class="col text-right px-0">
                        <button class="btn btn-primary" type="submit">Confirm</button>
                    </div>
                    
                    {{-- CANCEL BUTTON --}}
                    <div class="col text-left px-0">
                        <a href="{{ route("users.show", compact("user")) }}">
                            <button type="button" class="btn btn-secondary">Cancel</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="form-group my-5">
            <!-- CHANGE PASSWORD FORM -->
            <div class="row my-2">
                <div class="col">
                    <h4>Change password</h4>
                </div>
            </div>
            <form id="form-password" class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                @csrf
                @method("PATCH")
                <input type="hidden" name="process" value="changepassword">

                <div class="row mb-2">
                    <div class="offset-2 col-4 text-right">
                        <input id="new_password" type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Insert new password" required>
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    <div class="col-4 text-left">
                        <input id="confirm_new_password" type="password" name="confirm_new_password" class="form-control @error('confirm_new_password') is-invalid @enderror" placeholder="Confirm new password" required>
                        @error('confirm_new_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->rank !== -1)
                    <div class="row mt-3 mb-2">
                        <div class="offset-4 col-4">
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Insert current password" required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="row">
                    {{-- CONFIRM BUTTON --}}
                    <div class="col text-right px-0">
                        <button class="btn btn-primary" type="submit">Confirm</button>
                    </div>
                    
                    {{-- CANCEL BUTTON --}}
                    <div class="col text-left px-0">
                        <a href="{{ route("users.show", compact("user")) }}">
                            <button type="button" class="btn btn-secondary">Cancel</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection