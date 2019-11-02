@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

	<form method="POST" action="{{route("networks.store", ['user' => $user])}}">
		@csrf
		<div class="container col-md-6">
			<h2 class="mb-5 text-center">Build new Model</h2>

            <div class="form-group row"> {{-- Model type --}}
                <label for="model_type" class="col-md-4 col-form-label text-md-right font-weight-bold">Model type</label>
                <div class="col-md-8">
                    <select class="form-control @error('model_type') is-invalid @enderror" id="model_type" name="model_type" readonly>
                        <option value="Sequential">Sequential</option>
                    </select>

                    @error('model_type')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>

	        {{-- Title field --}}
			<div class="form-group row">
				<label for="title" class="col-md-4 col-form-label text-md-right font-weight-bold">{{ __('Title') }}</label>

				<div class="col-md-8">
					<input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="Insert model title" required autofocus>

					@error('title')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Description field --}}
			<div class="form-group row">
				<label for="description" class="col-md-4 col-form-label align-self-center text-md-right font-weight-bold">{{ __('Description') }}</label>

				<div class="col-md-8">
					<textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Insert model description">{{ old('description') }}</textarea>

					@error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			<div class="form-group row">
				
				{{-- X_Shape field --}}
				<div class="col-md-4 text-md-right">
					<label for="input_shape" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape') }}</label><br>
					<input id="input_shape" type="number" class="col-xl-8 form-control float-right @error('input_shape') is-invalid @enderror" name="input_shape" required value="1" step="1" min="1" max="1000">
					@error('input_shape')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				{{-- Y_Classes field --}}
				<div class="col-md-4 text-md-right">
					<label for="output_classes" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes') }}</label><br>
					<input id="output_classes" type="number" class="col-xl-8 form-control float-right @error('output_classes') is-invalid @enderror" name="output_classes" required value="1" step="1" min="1" max="1000">
					@error('output_classes')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
				
				{{-- Layers number --}}
                <div class="col-md-4 text-md-right">
                    <label for="layers_number" class="col-form-label align-self-center font-weight-bold">{{ __('Layers number') }}</label><br>
                    <input id="layers_number" type="number" class="col-xl-8 form-control float-right @error('layers_number') is-invalid @enderror" name="layers_number" required value="3" step="1" min="1" max="100">
                    @error('layers_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
			</div>
		</div>

		<div class="container-fluid">
			{{-- Layers --}}
			@include('layers.create')

			{{-- Submit button --}}
			<div class="form-group row">
				<div class="col text-center">
					<button type="submit" class="btn btn-primary">
						{{ __('Build model') }} &ensp; <span class="font-weight-bold h5">></span>
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection
