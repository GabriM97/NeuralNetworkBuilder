@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')
	<div class="container col-md-5">
		<h2 class="mb-5">Create new Dataset</h2>
		<form method="POST" enctype="multipart/form-data" action="{{route("datasets.store", ['user' => $user])}}">
			@csrf

			{{-- Import dataset field --}}
			<div class="form-group row">
				<div class="offset-md-2">Dataset</div>
				
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
 
				<div class="custom-file offset-md-2 mr-3">
					<input type="file" class="custom-file-input" id="dataset_file" name="dataset_file" accept=".json, .csv, .pickle, .pkl" required>
					<label class="custom-file-label" for="dataset_file">Choose file</label>
				</div> 
			</div>

			{{-- Title field --}}
			<div class="form-group row">
				<label for="data-title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

				<div class="col-md-8">
					<input id="data-title" type="text" class="form-control @error('data_name') is-invalid @enderror" name="data_title" value="{{ old('data_name') }}" required autofocus>

					@error('data_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Description field --}}
			<div class="form-group row">
				<label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

				<div class="col-md-8">
					<textarea id="description" class="form-control @error('data_description') is-invalid @enderror" name="description" value="{{ old('data_description') }}"></textarea>

					@error('data_description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-6 text-md-right">
					{{-- X_Shape field --}}
					<label for="input_shape" class="col-form-label pr-2">{{ __('Input shape') }}</label><br>
					<input id="input_shape" type="number" class="col-md-6 form-control float-right" name="input_shape" required value="1" step="1" min="1" max="1000">
				</div>
				<div class="col-md-6 text-md-right">
					{{-- Y_Classes field --}}
					<label for="output_classes" class="col-form-label pr-2">{{ __('Output classes') }}</label><br>
					<input id="output_classes" type="number" class="col-md-6 form-control float-right" name="output_classes" required value="1" step="1" min="1" max="1000">
				</div>
			</div>

			{{-- Train, Test or Both --}}
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4 pt-3 text-md-right">Data Type</legend>
					<div class="col-sm-6">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="dataset_type" id="train" value="train" >
							<label class="form-check-label" for="train">For Train 
								<div class="tooltip tooltip-custom">
									<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
									<span class="tooltiptext">Dataset: &ensp; <strong>{ X_train, Y_train }</strong></span>
								</div>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="dataset_type" id="test" value="test">
							<label class="form-check-label" for="test">For test
								<div class="tooltip tooltip-custom">
									<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
									<span class="tooltiptext">Dataset: &ensp; <strong>{ X_test, Y_test }</strong></span>
								</div>
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="dataset_type" id="generic" value="generic" checked>
							<label class="form-check-label" for="generic">Both
								<div class="tooltip tooltip-custom">
									<img src="{{ asset('img/info_icon.png') }}" alt="info_icon" class="info">
									<span class="tooltiptext">Dataset: &ensp; <strong>{ X_train, Y_train, X_test, Y_test }</strong></span>
								</div>
							</label>
						</div>
					</div>
				</div>
			</fieldset>

			{{-- Submit button --}}
			<div class="form-group row mb-0">
				<div class="col-md-6 offset-md-4">
					<button type="submit" class="btn btn-primary">
						{{ __('Import Dataset') }}
					</button>
				</div>
			</div>
		</form>
	</div>
@endsection