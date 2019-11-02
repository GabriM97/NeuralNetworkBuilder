@extends('layouts.app')

@section('page-title', $title)

@section('content')

    <div class="container col-5">

        <div class="row mb-5">
            <div class="col">
                <h2 class="content-title text-center">Edit Dataset</h2>
            </div>
        </div>

        <form class="form-edit" method="POST" action="{{route("datasets.update", compact("user", "dataset"))}}">
            @csrf
            @method("PATCH")
            
            <div class="form-group row mb-3"> {{-- TITLE --}}
                <label for="title" class="col text-right col-form-label font-weight-bold">Title</label>
                <div class="col-8">
                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{$dataset->data_name}}" required autofocus>
                
                    @error('title')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>
            
            <div class="form-group row mb-3"> {{-- DESCRIPTION --}}
                <label for="description" class="col-4 col-form-label align-self-center text-right font-weight-bold">Description</label>
                <div class="col-8 text-left">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{$dataset->data_description}}</textarea>
                
                    @error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>


            <div class="form-group row">    {{-- X_Shape | Y_Classes --}}
                <div class="col-md-6 text-md-right">
                    {{-- X_Shape field --}}
                    <label for="x_input" class="col-form-label pr-2 align-self-center font-weight-bold">{{ __('Input shape ( X_features )') }}</label><br>
                    <input id="x_input" type="number" class="col-md-6 form-control float-right @error('x_input') is-invalid @enderror" name="x_input" required value="{{$dataset->x_shape}}" step="1" min="1" max="1000">
                    
                    @error('x_input')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-6 text-md-right">
                    {{-- Y_Classes field --}}
                    <label for="y_output" class="col-form-label pr-2 align-self-center font-weight-bold">{{ __('Output classes ( Y_labels )') }}</label><br>
                    <input id="y_output" type="number" class="col-md-6 form-control float-right @error('y_output') is-invalid @enderror" name="y_output" required value="{{$dataset->y_classes}}" step="1" min="1" max="1000">
                    
                    @error('y_output')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Train, Test or Both --}}
			<fieldset class="form-group row">
                <div class="row">
                    <legend class="col-form-label col-sm-5 text-sm-right align-self-center font-weight-bold">Data Type</legend>
                    <div class="col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="train" value="train" @if($dataset->is_train) checked @endif>
                            <label class="form-check-label" for="train">For Train 
                                <div class="tooltip tooltip-custom">
                                    <img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
                                    <span class="tooltiptext">Dataset: &ensp; <strong>{ X_train, Y_train }</strong></span>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="test" value="test" @if($dataset->is_test) checked @endif>
                            <label class="form-check-label" for="test">For test
                                <div class="tooltip tooltip-custom">
                                    <img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
                                    <span class="tooltiptext">Dataset: &ensp; <strong>{ X_test, Y_test }</strong></span>
                                </div>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="generic" value="generic" @if($dataset->is_generic) checked @endif>
                            <label class="form-check-label" for="generic">Both
                                <div class="tooltip tooltip-custom">
                                    <img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
                                    <span class="tooltiptext">Dataset: &ensp; <strong>{ X_train, Y_train, X_test, Y_test }</strong></span>
                                </div>
                            </label>
                        </div>
                        @error('dataset_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <div class="form-group row">  
                {{-- CONFIRM BUTTON --}}
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