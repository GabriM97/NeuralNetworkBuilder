@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('scripts')
    <script src="{{ asset('js/generate_layers.js') }}"></script>
    <script src="{{ asset('js/handle_submit.js') }}"></script>
@endsection

@section('content')
	<div class="container">
		<div class="row">
			<div class="col h5">
				<a class="text-decoration-none rounded text-white p-md-2" href="{{route("networks.index", compact("user"))}}">
						<i class="fas fa-arrow-circle-left mr-2"></i>Models
				</a>
			</div>
		</div>
	</div>

	<form id="main-form" method="POST" action="{{route("networks.store", ['user' => $user])}}">
		@csrf
		<div class="main-container rounded container col-md-5 p-2">
			<h2 class="mb-5 mt-3 text-center">Build new Model<i class="fas fa-project-diagram fa-xs pl-3"></i></h2>

            <div class="form-group row px-5"> {{-- Model type --}}
                <label for="model_type" class="col-md-3 col-form-label text-md-right font-weight-bold">Model type</label>
                <div class="col-md">
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
			<div class="form-group row px-5">
				<label for="title" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Title') }}</label>

				<div class="col-md">
					<input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="Insert model title" required autofocus>

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
					<textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Insert model description">{{ old('description') }}</textarea>

					@error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			<div class="form-group row px-5">
				{{-- X_Shape field --}}
				<div class="col-md text-md-center">
					<label for="input_shape" class="col-form-label align-self-center font-weight-bold">{{ __('Input shape') }}</label><br>
					<input id="input_shape" type="number" class="col-md-10 mx-md-auto form-control @error('input_shape') is-invalid @enderror" name="input_shape" required value="1" step="1" min="1" max="1000">
					@error('input_shape')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				{{-- Y_Classes field --}}
				<div class="col-md text-md-center">
					<label for="output_classes" class="col-form-label align-self-center font-weight-bold">{{ __('Output classes') }}</label><br>
					<input id="output_classes" type="number" class="col-md-10 mx-md-auto form-control @error('output_classes') is-invalid @enderror" name="output_classes" required value="1" step="1" min="1" max="1000">
					@error('output_classes')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
				
				{{-- Layers number --}}
                <div class="col-md text-md-center">
                    <label for="layers_number" class="col-form-label align-self-center font-weight-bold">{{ __('Layers number') }}</label><br>
                    <input id="layers_number" type="number" class="col-md-10 mx-md-auto form-control @error('layers_number') is-invalid @enderror" name="layers_number" required value="3" step="1" min="1" max="100">
                    @error('layers_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
			</div>

			{{-- Submit button --}}
			<div class="form-group row mt-5">
				<div class="col text-center">
					<button id="upload-button" type="submit" class="btn btn-info">
						<i class="fas fa-project-diagram fa-lg mr-2"></i>{{ __('Build model') }}<i class="fas fa-arrow-circle-right fa-lg ml-3"></i>
					</button>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			{{-- Layers --}}
			@include('layers.create')
		</div>
	</form>
@endsection
