@extends('layouts.app')

@section('page-title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-2 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("networks.show", compact("user","network"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Model
                </a>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-5 text-md-center p-2 my-4">
        <h2 class="content-title mb-5 mt-3 text-center">Edit Model<i class="fas fa-pen fa-xs pl-3"></i></h2>

        <form class="form-edit px-5" method="POST" action="{{route("networks.update", compact("user", "network"))}}">
            @csrf
            @method("PATCH")
            
            <div class="row form-group my-3">
                {{-- TITLE --}}
                <label for="title" class="col-md-3 col-form-label text-md-right font-weight-bold">Title</label>
                <div class="col-md">
                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{$network->model_name}}" required autofocus>
                    
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row form-group my-3">
                {{-- DESCRIPTION --}}
                <label for="description" class="col-md-3 col-form-label align-self-center text-md-right font-weight-bold">Description</label>
                <div class="col-md">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{$network->model_description}}</textarea>
                </div>
            </div>
            
            {{--
            <div class="form-group row my-3">
                {{-- INPUT CLASSES -}}
                <div class="col-md text-md-center">
                    <label for="input_shape" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape') }}</label><br>
                    <input id="input_shape" type="number" class="col-md-10 mx-md-auto form-control @error('input_shape') is-invalid @enderror" name="input_shape" required value="{{$network->input_shape}}" step="1" min="1" max="1000">
                    @error('input_shape')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- OUTPUT_CLASSES -}}
                <div class="col-md text-md-center">
                    <label for="output_classes" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes') }}</label><br>
                    <input id="output_classes" type="number" class="col-md-10 mx-md-auto form-control @error('output_classes') is-invalid @enderror" name="output_classes" required value="{{$network->output_classes}}" step="1" min="1" max="1000">
                    @error('output_classes')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                {{-- LAYERS_NUMBER -}}
                <div class="col-md text-md-center">
                    <label for="layers_number" class="col-form-label align-self-center font-weight-bold">{{ __('Layers number') }}</label><br>
                    <input id="layers_number" type="number" class="col-md-10 mx-md-auto form-control @error('layers_number') is-invalid @enderror" name="layers_number" required value="{{$network->layers_number}}" step="1" min="1" max="100">
                    @error('layers_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            {{-- LAYERS -}}
            <div class="form-group my-5"> 
                @include('layers.edit')
            </div>
            --}}

            <div class="row">
                {{-- CONFIRM BUTTON --}}
                <div class="col text-right">
                    <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                </div>

                {{-- CANCEL BUTTON --}}
                <div class="col text-left">
                    <a href="{{ route("networks.show", compact("user", "network")) }}">
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