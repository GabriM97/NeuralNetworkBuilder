{{-- ADMIN ONLY --}}

@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends('layouts.app')

@section('page-title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-2 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("nodes.show", compact("node"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Node
                </a>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-5 text-md-center p-2 my-4">
        <h2 class="content-title mb-5 mt-3 text-center">Edit Node<i class="fas fa-pen fa-xs pl-3"></i></h2>

        <form class="form-edit px-5" method="POST" action="{{route("nodes.update", compact("node"))}}">
            @csrf
            @method("PATCH")
            
            <div class="form-group row my-3"> {{-- IP Address --}}
                <label for="ip_address" class="col-md-3 text-md-right col-form-label font-weight-bold">IP Address</label>
                <div class="col-md">
                    <input id="ip_address" type="text" class="form-control @error('ip_address') is-invalid @enderror" name="ip_address" value="{{$node->ip_address}}" required autofocus>
                
                    @error('ip_address')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>
            
            <div class="form-group row my-3"> {{-- DESCRIPTION --}}
                <label for="description" class="col-md-3 col-form-label align-self-center text-md-right font-weight-bold">Description</label>
                <div class="col-md">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{$node->description}}</textarea>
                
                    @error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>

            <div class="form-group row">  
                {{-- CONFIRM BUTTON --}}
                <div class="col text-right">
                    <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                </div>

                {{-- CANCEL BUTTON --}}
                <div class="col text-left">
                    <a href="{{ route("nodes.show", compact("node")) }}">
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
@endsection