@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('scripts')
    <script src="{{ asset('js/dataset_uploader.js') }}"></script>
@endsection

@section('styles')
	<link href="{{ asset('css/progress_bar.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md h5">
				<a class="text-decoration-none rounded text-white p-md-2" href="{{route("datasets.index", compact("user"))}}">
					<i class="fas fa-arrow-circle-left mr-2"></i>Datasets
				</a>
			</div>
		</div>
	</div>

	<form id="main-form" method="POST" enctype="multipart/form-data" action="{{route("datasets.store", ['user' => $user])}}">
		@csrf
		<div class="main-container rounded container col-md-5 p-2">
			<h2 class="mb-5 mt-3 text-center">Create new Dataset<i class="fas fa-list fa-sm pl-3"></i></h2>

			{{-- Import dataset field --}}
			<div class="form-group row px-5">
				<div class="font-weight-bold">Dataset</div>
				
				<div class="tooltip tooltip-custom">
					<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
					<span class="tooltiptext">
						Dataset accepted extensions: .csv, .json, .pkl or .pickle<br>
						Download the example dataset to understand how your dataset have to be:<br>
						<a href="{{ asset('example_dataset/data.csv') }}">CSV</a>,
						<a href="{{ asset('example_dataset/data.json') }}">JSON</a>,
						<a href="{{ asset('example_dataset/data.pkl') }}">PKL/PICKLE</a>.
					</span>
				</div>
				
				{{-- Upload field --}}
				<div class="custom-file">
					<div id="progressBar"></div>
					<input type="file" class="custom-file-input @error('dataset_file') is-invalid @enderror" id="dataset_file" name="dataset_file" accept=".json, .csv, .pickle, .pkl" required>
					<label class="custom-file-label" for="dataset_file">Choose file</label>

					@error('dataset_file')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Title field --}}
			<div class="form-group row px-5">
				<label for="title" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Title') }}</label>

				<div class="col-md">
					<input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="Insert dataset title" required autofocus>

					@error('title')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Description field --}}
			<div class="form-group row px-5">
				<label for="description" class="col-md-3 col-form-label align-self-center text-md-right font-weight-bold">{{ __('Description') }}</label>

				<div class="col-md">
					<textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Insert dataset description">{{ old('description') }}</textarea>

					@error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			<div class="form-group row px-5">
				<div class="col-md-6 text-md-center">
					{{-- X_Shape field --}}
					<label for="input_shape" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape ( X_features )') }}</label><br>
					<input id="input_shape" type="number" class="col-md-6 mx-md-auto form-control @error('input_shape') is-invalid @enderror" name="input_shape" required value="1" step="1" min="1" max="1000">
					
					@error('input_shape')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
				<div class="col-md-6 text-md-center">
					{{-- Y_Classes field --}}
					<label for="output_classes" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes ( Y_labels )') }}</label><br>
					<input id="output_classes" type="number" class="col-md-6 mx-md-auto form-control @error('output_classes') is-invalid @enderror" name="output_classes" required value="1" step="1" min="1" max="1000">
					
					@error('output_classes')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Train, Test or Both --}}
			<fieldset class="form-group row px-5">
				<div class="row">
					<legend class="col-form-label col-md-5 text-md-right align-self-center font-weight-bold">Data Type</legend>
					<div class="col-md-6">
						<div class="form-check">
							<input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="train" value="train" >
							<label class="form-check-label" for="train">For Train 
								<div class="tooltip tooltip-custom">
									<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
									<span class="tooltiptext">Dataset: &ensp; <strong>{ X_train, Y_train }</strong></span>
								</div>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="test" value="test">
							<label class="form-check-label" for="test">For test
								<div class="tooltip tooltip-custom">
									<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
									<span class="tooltiptext">Dataset: &ensp; <strong>{ X_test, Y_test }</strong></span>
								</div>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input @error('dataset_type') is-invalid @enderror" type="radio" name="dataset_type" id="generic" value="generic" checked>
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

			{{-- Submit button --}}
			<div class="form-group row mb-0">
				<div class="col-md text-center">
					<button id="upload-button" type="submit" class="btn btn-info">
						<i class="fas fa-upload fa-lg mr-2"></i>{{ __('Upload Dataset') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection