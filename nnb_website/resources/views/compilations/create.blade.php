@if((Auth::user()->id != $user->id))
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')
	<form method="POST" action="{{route("compilations.store", compact("user", "network"))}}">
		@csrf
		<div class="container col-md-6">
        	<h2 class="mb-5 text-center">Compile Model | {{$network->model_name}}</h2>

            <div class="form-group row"> {{-- Learning rate --}}
                <label for="learning_rate" class="col-md-4 offset-2 col-form-label text-md-right">Learning Rate</label>
                <div class="col-md-4">
                    <select class="form-control @error('learning_rate') is-invalid @enderror" id="learning_rate" name="learning_rate">
                        <option value="0.0001">0.0001</option>
                        <option value="0.001">0.001</option> 
                        <option value="0.003">0.003</option>
                        <option value="0.01" selected>0.01</option>
                        <option value="0.03">0.03</option>
                        <option value="0.1">0.1</option> 
                        <option value="0.3">0.3</option>
                        <option value="1">1</option>
                        <option value="3">3</option> 
                        <option value="10">10</option>
                    </select>

                    @error('learning_rate')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>

	        {{-- Optimizer --}}
			<div class="form-group row">
				<label for="optimizer" class="col-md-4 offset-2 col-form-label text-md-right">{{ __('Optimizer') }}</label>

				<div class="col-md-4">
					<select class="form-control @error('optimizer') is-invalid @enderror" id="optimizer" name="optimizer">
                        <option value="adam" selected>Adam</option>
                        <option value="sgd">SGD</option>
                    </select>

					@error('optimizer')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Metrics list --}}
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4 offset-2 text-sm-right align-self-center">Metrics list</legend>
					<div class="col-sm-4 py-2">
						<div class="form-check">
							<input class="form-check-input @error('metrics_list') is-invalid @enderror" type="checkbox" name="metrics_list[]" id="accuracy" value="accuracy" checked>
							<label class="form-check-label" for="accuracy">Accuracy</label>
						</div>
						
						@error('metrics_list')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
				</div>
			</fieldset>

			{{-- Submit button --}}
			<div class="form-group row">
				<div class="col text-center">
					<button type="submit" class="btn btn-warning">
						{{ __('Compile model') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection
