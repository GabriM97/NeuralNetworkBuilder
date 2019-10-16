@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    <!-- If user's available space is 0, user can't start new training -->
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)  

@section('content')
	<form method="POST" action="{{route("trainings.store", ['user' => $user])}}">
		@csrf
		<div class="container col-md-6">
			<h2 class="mb-5 text-center">Start new training</h2>

            <div class="form-group row"> {{-- Model selection --}}
                <label for="model_id" class="col-md-4 col-form-label text-md-right">Model</label>
                <div class="col-md-8">
                    <select class="form-control @error('model_id') is-invalid @enderror" id="model_id" name="model_id">
                        @foreach ($models as $model)
                            <option value="{{$model->id}}">{{$model->model_name}}</option>
                        @endforeach
                    </select>

                    @error('model_id')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>

	        {{-- Title field --}}
			<div class="form-group row">
				<label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

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
				<label for="description" class="col-md-4 col-form-label align-self-center text-md-right">{{ __('Description') }}</label>

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
					<label for="input_shape" class="col-form-label align-self-center">{{ __('Input shape') }}</label><br>
					<input id="input_shape" type="number" class="col-xl-8 form-control float-right @error('input_shape') is-invalid @enderror" name="input_shape" required value="1" step="1" min="1" max="1000">
					@error('input_shape')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				{{-- Y_Classes field --}}
				<div class="col-md-4 text-md-right">
					<label for="output_classes" class="col-form-label align-self-center">{{ __('Output classes') }}</label><br>
					<input id="output_classes" type="number" class="col-xl-8 form-control float-right @error('output_classes') is-invalid @enderror" name="output_classes" required value="1" step="1" min="1" max="1000">
					@error('output_classes')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
				
				{{-- Layers number --}}
                <div class="col-md-4 text-md-right">
                    <label for="layers_number" class="col-form-label align-self-center">{{ __('Layers number') }}</label><br>
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
						{{ __('Build model') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection
