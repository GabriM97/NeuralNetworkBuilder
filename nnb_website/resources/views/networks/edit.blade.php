@extends('layouts.app')

@section('page-title', $title)

@section('content')

<div class="container text-center">

    <div class="row mb-4">
        <div class="col">
            <h2 class="content-title d-inline-block">Edit Model</h2>
        </div>
    </div>

    <form class="form-edit" method="POST" action="{{route("networks.update", compact("user", "network"))}}">
        @csrf
        @method("PATCH")
        
        <div class="row form-group"> {{-- TITLE --}}
            <div class="col text-right font-weight-bold">Title</div>
            <div class="col text-left">
                <input type="text" class="" name="title" value="{{$network->model_name}}">
            </div>
        </div>
        <div class="row form-group"> {{-- DESCRIPTION --}}
            <div class="col text-right font-weight-bold">Description</div>
            <div class="col text-left">
                <textarea name="description" rows="4">{{$network->model_description}}</textarea>
            </div>
        </div>

        <div class="row form-group"> 
            <div class="col-2 offset-3"> {{-- INPUT_SHAPE --}}
                <div class="row text-right font-weight-bold">
                    <div class="col">Input shape</div>
                </div>
                <div class="row text-right">
                    <div class="col"><input type="number" name="input_shape" value="{{$network->input_shape}}" min="1" max="1000" step="1"></div>
                </div>
            </div>
            <div class="col-2"> {{-- OUTPUT_CLASSES --}}
                <div class="row text-center font-weight-bold">
                    <div class="col">Output classes</div>
                </div>
                <div class="row text-center">
                    <div class="col"><input type="number" name="output_shape" value="{{$network->output_classes}}" min="1" max="1000" step="1"></div>
                </div>
            </div>
            <div class="col-2"> {{-- LAYERS_NUMBER --}}
                <div class="row text-left font-weight-bold">
                    <div class="col">Layers number</div>
                </div>
                <div class="row text-left">
                    <div class="col"><input type="number" name="layers_number" value="{{$network->layers_number}}" min="1" max="100" step="1"></div>
                </div>
            </div>
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