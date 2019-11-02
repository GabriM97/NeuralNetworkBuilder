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

			@php	//if network->is_compiled
				if($compile = App\Compilation::where("model_id", $network->id)->first())
					$status = array("$compile->learning_rate" => 1);			
				else
					$status = array("0.01" => 1);
			@endphp

            <div class="form-group row"> {{-- Learning rate --}}
                <label for="learning_rate" class="col-md-4 offset-2 col-form-label text-md-right font-weight-bold">Learning Rate</label>
                <div class="col-md-4">
                    <select class="form-control @error('learning_rate') is-invalid @enderror" id="learning_rate" name="learning_rate">
						<option value="0.0001" @if(isset($status["0.0001"])) selected @endif>0.0001</option>
						<option value="0.0003" @if(isset($status["0.0003"])) selected @endif>0.0003</option>
                        <option value="0.001" @if(isset($status["0.001"])) selected @endif>0.001</option> 
                        <option value="0.003" @if(isset($status["0.003"])) selected @endif>0.003</option>
                        <option value="0.01" @if(isset($status["0.01"])) selected @endif>0.01</option>
                        <option value="0.03" @if(isset($status["0.03"])) selected @endif>0.03</option>
                        <option value="0.1" @if(isset($status["0.1"])) selected @endif>0.1</option> 
                        <option value="0.3" @if(isset($status["0.3"])) selected @endif>0.3</option>
                        <option value="1" @if(isset($status["1"])) selected @endif>1</option>
                        <option value="3" @if(isset($status["3"])) selected @endif>3</option> 
                        <option value="10" @if(isset($status["10"])) selected @endif>10</option>
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
				<label for="optimizer" class="col-md-4 offset-2 col-form-label text-md-right font-weight-bold">{{ __('Optimizer') }}</label>

				<div class="col-md-4">
					<select class="form-control @error('optimizer') is-invalid @enderror" id="optimizer" name="optimizer">
                        <option value="adam" {{ ($compile && $compile->optimizer == "adam") ? "selected" : "" }}>Adam</option>
                        <option value="sgd" {{ ($compile && $compile->optimizer == "sgd") ? "selected" : "" }}>SGD</option>
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
					<legend class="col-form-label col-sm-4 offset-2 text-sm-right align-self-center font-weight-bold">Metrics list</legend>
					<div class="col-sm-4 py-2">
						<div class="form-check">
							<input class="form-check-input @error('metrics_list') is-invalid @enderror" type="checkbox" name="metrics_list[]" id="accuracy" value="accuracy" {{(($compile && $compile->metrics) || (!$compile)) ? "checked" : ""}}>
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
