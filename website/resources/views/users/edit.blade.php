@extends('layouts.app')

@section('page-title', $title)

@section('scripts')
    <script src="{{ asset('js/edit_form_control.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-2 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("users.show", compact("user"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Profile
                </a>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-6 text-md-center p-2 my-4">
        <h2 class="content-title mb-5 mt-3 text-center"><i class="fas fa-pen fa-xs pr-3"></i>Edit profile | {{ $user->username }}</h2>

        @if(Auth::user()->rank == -1)
            <!-- CHANGE USERNAME FORM (ADMIN ONLY) -->
            <div class="form-group my-5 px-5">
                <div class="row my-2">
                    <div class="col-md">
                        <h4>Change username<i class="fas fa-signature fa-xs pl-2"></i></h4>
                    </div>
                </div>
                <form class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                    @csrf
                    @method("PATCH")
                    <input type="hidden" name="process" value="changeusername">

                    <div class="form-group row mb-2">
                        <label for="username" class="col-md-3 col-form-label pr-0 text-md-right font-weight-bold">Username</label>
                        <div class="col-md text-left">
                            <input id="username" type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ $user->username }}" required>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        {{-- CONFIRM BUTTON --}}
                        <div class="col text-right px-0">
                            <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                        </div>

                        {{-- CANCEL BUTTON --}}
                        <div class="col text-left px-0">
                            <a href="{{ route("users.show", compact("user")) }}">
                                <button type="button" class="btn btn-secondary">
                                    Cancel
                                    <span class="fa-stack">
                                        <i class="fas fa-pen fa-stack-1x"></i>
                                        <i class="far fa-circle fa-stack-2x"></i>
                                        <i class="fas fa-slash fa-stack-1x"></i>
                                    </span>
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- CHANGE EMAIL FORM -->  
        <div class="form-group my-5 px-5">    
            <div class="row my-2">
                <div class="col-md">
                    <h4 class="d-inline-block"><i class="fas fa-envelope fa-xs pr-2"></i>Change email</h4> &ensp;<span>(current: {{ $user->email }})</span>
                </div>
            </div>
            <form id="form-email" class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                @csrf
                @method("PATCH")
                <input type="hidden" name="process" value="changeemail">

                <div class="form-group row mb-2">
                    <div class="col-md text-md-right my-2"> {{-- New email --}}
                        <input type="email" id="new_email" name="new_email" class="form-control @error('new_email') is-invalid @enderror" placeholder="Insert new email" required>
                        @error('new_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md text-left my-2"> {{-- Confirm new email --}}
                        <input type="email" id="confirm_new_email" name="confirm_new_email" class="form-control @error('confirm_new_email') is-invalid @enderror" placeholder="Confirm new email" required>
                        @error('confirm_new_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->rank !== -1)
                    <div class="form-group row mt-md-3 mb-2">
                        <div class="offset-md-3 col-md-6">    {{-- Current password --}}
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Insert current password" required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="form-group row">
                    {{-- CONFIRM BUTTON --}}
                    <div class="col text-right px-0">
                        <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                    </div>

                    {{-- CANCEL BUTTON --}}
                    <div class="col text-left px-0">
                        <a href="{{ route("users.show", compact("user")) }}">
                            <button type="button" class="btn btn-secondary">
                                Cancel
                                <span class="fa-stack">
                                    <i class="fas fa-pen fa-stack-1x"></i>
                                    <i class="far fa-circle fa-stack-2x"></i>
                                    <i class="fas fa-slash fa-stack-1x"></i>
                                </span>
                            </button>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- CHANGE PASSWORD FORM -->
        <div class="form-group my-5 px-5">
            <div class="row my-2">
                <div class="col-md">
                    <h4><i class="fas fa-key fa-xs pr-2"></i>Change password</h4>
                </div>
            </div>
            <form id="form-password" class="form-edit" method="POST" action="{{route("users.update", ["user" => $user])}}">
                @csrf
                @method("PATCH")
                <input type="hidden" name="process" value="changepassword">

                <div class="form-group row mb-2">
                    <div class="col-md text-md-right my-2">
                        <input id="new_password" type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Insert new password" required>
                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    <div class="col-md text-left my-2">
                        <input id="confirm_new_password" type="password" name="confirm_new_password" class="form-control @error('confirm_new_password') is-invalid @enderror" placeholder="Confirm new password" required>
                        @error('confirm_new_password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->rank !== -1)
                    <div class="form-group row mt-md-3 mb-2">
                        <div class="offset-md-3 col-md-6">
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Insert current password" required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="form-group row">
                    {{-- CONFIRM BUTTON --}}
                    <div class="col text-right px-0">
                        <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                    </div>

                    {{-- CANCEL BUTTON --}}
                    <div class="col text-left px-0">
                        <a href="{{ route("users.show", compact("user")) }}">
                            <button type="button" class="btn btn-secondary">
                                Cancel
                                <span class="fa-stack">
                                    <i class="fas fa-pen fa-stack-1x"></i>
                                    <i class="far fa-circle fa-stack-2x"></i>
                                    <i class="fas fa-slash fa-stack-1x"></i>
                                </span>
                            </button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection