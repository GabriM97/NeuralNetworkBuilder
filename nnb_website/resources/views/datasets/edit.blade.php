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
        
        <div class="row"> {{-- TITLE --}}
            <div class="col text-right font-weight-bold">Title</div>
            <div class="col text-left">
                <input type="text" class="" name="title" value="{{$dataset->data_name}}">
            </div>
        </div>
        <div class="row"> {{-- DESCRIPTION --}}
            <div class="col text-right font-weight-bold">Description</div>
            <div class="col text-left">
                <textarea name="description">{{$dataset->description}}</textarea>
            </div>
        </div>

        <div class="row"> {{-- DATA TYPE --}}
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

        <div class="row">  {{-- CONFIRM BUTTON --}}
            <div class="col text-right"><button class="btn btn-primary" type="submit">Confirm</button></div>
            <div class="col text-left"><button class="btn btn-secondary" type="submit">Cancel</button></div>
        </div>
    </form>
</div>
@endsection