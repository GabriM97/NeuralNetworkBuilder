@extends('layouts.app')

@section('page-title', $title)

@section('content')

<div class="container text-center">

    <div class="row mb-4">
        <div class="col">
            <h2 class="content-title d-inline-block">Edit Dataset</h2>
        </div>
    </div>

    <form class="form-edit" method="POST" action="{{route("datasets.update", compact("user", "dataset"))}}">
        @csrf
        @method("PATCH")
        
        <div class="row mb-3"> {{-- TITLE --}}
            <div class="col text-right font-weight-bold">Title</div>
            <div class="col text-left">
                <input type="text" class="" name="title" value="{{$dataset->data_name}}">
            </div>
        </div>
        <div class="row mb-3"> {{-- DESCRIPTION --}}
            <div class="col text-right font-weight-bold">Description</div>
            <div class="col text-left">
                <textarea name="description" rows="4">{{$dataset->data_description}}</textarea>
            </div>
        </div>

        <div class="row mb-3"> {{-- DATA TYPE --}}
            <div class="col text-right font-weight-bold">Data Type</div>
            <div class="col text-left">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="dataset_type" id="train" value="train" @if($dataset->is_train) checked @endif>
                    <label class="form-check-label" for="train"> {{-- train --}}
                        For Train <img src="{{ asset('img/info_icon.png') }}" class="ml-2 info" alt="info_icon" data-toggle="tooltip" data-placement="right" title="{X_train, Y_train}">
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="dataset_type" id="test" value="test" @if($dataset->is_test) checked @endif>
                    <label class="form-check-label" for="test"> {{-- test --}}
                        For Test <img src="{{ asset('img/info_icon.png') }}" class="ml-2 info" alt="info_icon" data-toggle="tooltip" data-placement="right" title="{X_test, Y_test}">
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="dataset_type" id="generic" value="generic" @if($dataset->is_generic) checked @endif>
                    <label class="form-check-label" for="generic"> {{-- both --}}
                        Both <img src="{{ asset('img/info_icon.png') }}" class="ml-2 info" alt="info_icon" data-toggle="tooltip" data-placement="right" title="{X_train, Y_train, X_test, Y_test}">
                    </label>
                </div>
            </div>
        </div>

        <div class="row mb-3"> {{-- X_SHAPE | Y_CLASSES --}}
            <div class="col-6">
                <div class="row text-right font-weight-bold">
                    <div class="col offset-8">Input shape (X_features)</div>
                </div>
                <div class="row text-right">
                    <div class="col"><input type="number" name="x_input" value="{{$dataset->x_shape}}" min="1" max="1000" step="1"></div>
                </div>
            </div>
            <div class="col-2">
                <div class="row text-left font-weight-bold">
                    <div class="col">Output classes (Y_labels)</div>
                </div>
                <div class="row text-left">
                    <div class="col"><input type="number" name="y_output" value="{{$dataset->y_classes}}" min="1" max="1000" step="1"></div>
                </div>
            </div>
        </div>

        <div class="row">  {{-- CONFIRM BUTTON --}}
            <div class="col text-right"><button class="btn btn-primary" type="submit">Confirm</button></div>

            {{-- CANCEL BUTTON --}}
            <div class="col text-left">
                <a href="{{ route("datasets.show", compact("user", "dataset")) }}">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                </a>
            </div>
        </div>
    </form>
</div>
@endsection