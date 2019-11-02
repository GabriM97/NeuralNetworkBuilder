@extends('layouts.app')

@section('page-title', $title)

@section('content')

<div class="container col-5 text-center">

    <div class="row mb-4">
        <div class="col">
            <h2 class="content-title d-inline-block">Edit Model</h2>
        </div>
    </div>

    <form class="form-edit" method="POST" action="{{route("networks.update", compact("user", "network"))}}">
        @csrf
        @method("PATCH")
        
        <div class="row form-group"> {{-- TITLE --}}
            <label for="title" class="col col-form-label text-right font-weight-bold">Title</label>

            <div class="col-8 text-left">
                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{$network->model_name}}" required autofocus>
                
                @error('title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row form-group"> {{-- DESCRIPTION --}}
            <label for="description" class="col col-form-label align-self-center text-right font-weight-bold">Description</label>
            <div class="col-8 text-left">
                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{$network->model_description}}</textarea>
            </div>
        </div>

        <div class="form-group row">

            {{-- INPUT CLASSES --}}
            <div class="col-md-4 text-md-right">
                <label for="input_shape" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape') }}</label><br>
                <input id="input_shape" type="number" class="col-xl-8 form-control float-right @error('input_shape') is-invalid @enderror" name="input_shape" required value="{{$network->input_shape}}" step="1" min="1" max="1000">
                @error('input_shape')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- OUTPUT_CLASSES --}}
            <div class="col-md-4 text-md-right">
                <label for="output_classes" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes') }}</label><br>
                <input id="output_classes" type="number" class="col-xl-8 form-control float-right @error('output_classes') is-invalid @enderror" name="output_classes" required value="{{$network->output_classes}}" step="1" min="1" max="1000">
                @error('output_classes')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            {{-- LAYERS_NUMBER --}}
            <div class="col-md-4 text-md-right">
                <label for="layers_number" class="col-form-label align-self-center font-weight-bold">{{ __('Layers number') }}</label><br>
                <input id="layers_number" type="number" class="col-xl-8 form-control float-right @error('layers_number') is-invalid @enderror" name="layers_number" required value="{{$network->layers_number}}" step="1" min="1" max="100">
                @error('layers_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="my-5 form-group"> {{-- LAYERS --}}
            @include('layers.edit')
        </div>

        <div class="row">  {{-- CONFIRM BUTTON --}}
            <div class="col text-right"><button class="btn btn-primary" type="submit">Confirm</button></div>

            {{-- CANCEL BUTTON --}}
            <div class="col text-left">
                <a href="{{ route("networks.show", compact("user", "network")) }}">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                </a>
            </div>
        </div>
    </form>
</div>
@endsection