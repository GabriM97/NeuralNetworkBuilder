@extends('layouts.app')

@section('page-title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-2 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("datasets.show", compact("user","dataset"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Dataset
                </a>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-5 text-md-center p-2 my-4">
        <h2 class="content-title mb-5 mt-3 text-center">Edit Dataset<i class="fas fa-pen fa-xs pl-3"></i></h2>

        <form class="form-edit px-5" method="POST" action="{{route("datasets.update", compact("user", "dataset"))}}">
            @csrf
            @method("PATCH")
            
            <div class="form-group row my-3"> {{-- TITLE --}}
                <label for="title" class="col-md-3 text-md-right col-form-label font-weight-bold">Title</label>
                <div class="col-md">
                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{$dataset->data_name}}" required autofocus>
                
                    @error('title')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>
            
            <div class="form-group row my-3"> {{-- DESCRIPTION --}}
                <label for="description" class="col-md-3 col-form-label align-self-center text-md-right font-weight-bold">Description</label>
                <div class="col-md">
                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{$dataset->data_description}}</textarea>
                
                    @error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>


            <div class="form-group row my-3">    {{-- X_Shape | Y_Classes --}}
                <div class="col-md-6 text-md-center">
                    {{-- X_Shape field --}}
                    <label for="x_input" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape ( X_features )') }}</label><br>
                    <input id="x_input" type="number" class="col-md-6 mx-md-auto form-control @error('x_input') is-invalid @enderror" name="x_input" required value="{{$dataset->x_shape}}" step="1" min="1" max="1000">
                    
                    @error('x_input')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-6 text-md-center">
                    {{-- Y_Classes field --}}
                    <label for="y_output" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes ( Y_labels )') }}</label><br>
                    <input id="y_output" type="number" class="col-md-6 mx-md-auto form-control @error('y_output') is-invalid @enderror" name="y_output" required value="{{$dataset->y_classes}}" step="1" min="1" max="1000">
                    
                    @error('y_output')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Train, Test or Both --}}
			<fieldset class="form-group row m-3">
                <div class="row">
                    <legend class="col-form-label col-md-5 text-md-right align-self-center font-weight-bold">Data Type</legend>
                    <div class="col-md-6 text-left">
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
                <div class="col text-right">
                    <button class="btn btn-success text-dark" type="submit">Confirm<i class="fas fa-check-circle fa-lg ml-2"></i></button>
                </div>

                {{-- CANCEL BUTTON --}}
                <div class="col text-left">
                    <a href="{{ route("datasets.show", compact("user", "dataset")) }}">
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