@if((Auth::user()->id != $user->id) || ($user->available_space <= 0))
    <!-- If user's available space is 0, user can't start new training -->
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)  

@section('scripts')
    <script src="{{ asset('js/manage_training.js') }}"></script>
@endsection

@section('content')
	<div class="container">
		<div class="row">
			<div class="col h5">
				<a class="text-decoration-none rounded text-white p-md-2" href="{{route("trainings.index", compact("user"))}}">
					<i class="fas fa-arrow-circle-left mr-2"></i>Trainings
				</a>
			</div>
		</div>
	</div>

	<form method="POST" action="{{route("trainings.store", ['user' => $user])}}">
		@csrf
		<div class="main-container rounded container col-md-5 p-2">
			<h2 class="mb-5 mt-3 text-center">Make new training<i class="fas fa-tools fa-xs pl-3"></i></h2>

			{{-- Description field --}}
			<div class="form-group row px-5">
				<label for="description" class="col-md-3 col-form-label align-self-center text-md-right font-weight-bold">{{ __('Description') }}</label>
				<div class="col-md">
					<textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Insert training description">{{ old('description') }}</textarea>

					@error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>
			
			{{-- Model selection --}}
            <div class="form-group row px-5"> 
                <label for="model_id" class="col-md-3 col-form-label text-md-right font-weight-bold">Model</label>
                <div class="col-md">
                    <select class="form-control @error('model_id') is-invalid @enderror" id="model_id" name="model_id" required autofocus>
                        @foreach ($models as $model)
                            <option value="{{$model->id}}" x_inp="{{$model->input_shape}}" y_out="{{$model->output_classes}}">{{$model->model_name}}</option>
                        @endforeach
					</select>

                    @error('model_id')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
            </div>

	        {{-- Training Datasets field --}}
			<div class="form-group row px-5">
				<label for="training_dataset" class="col-md-3 col-form-label text-md-right font-weight-bold">Training Dataset</label>
				<div class="col-md">
					<select class="form-control @error('training_dataset') is-invalid @enderror" id="training_dataset" name="training_dataset" required>
						@foreach ($datasets as $dataset)
							@if ($dataset->is_train || $dataset->is_generic)
								<option value="{{$dataset->id}}" x_inp="{{$dataset->x_shape}}" y_out="{{$dataset->y_classes}}">{{$dataset->data_name}}</option>
							@endif
                        @endforeach
					</select>
					
					@error('training_dataset')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Test Dataset field --}}
			<div class="form-group row px-5">
				<label for="test_dataset" class="col-md-3 col-form-label text-md-right font-weight-bold">Test Dataset</label>
				<div class="col-md">
					<select class="form-control @error('test_dataset') is-invalid @enderror" id="test_dataset" name="test_dataset">
						<option value="" selected="selected">Do not evaluate</option>
						@foreach ($datasets as $dataset)
							@if ($dataset->is_test || $dataset->is_generic)
								<option value="{{$dataset->id}}" x_inp="{{$dataset->x_shape}}" y_out="{{$dataset->y_classes}}">{{$dataset->data_name}}</option>
							@endif
                        @endforeach
					</select>

					@error('test_dataset')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			<div class="form-group row px-5">
				{{-- Epochs field --}}
				<div class="col-md text-md-center">
					<label for="epochs" class="col-form-label align-self-center font-weight-bold">{{ __('Epochs') }}</label><br>
					<input id="epochs" type="number" class="col-md-10 mx-md-auto form-control @error('epochs') is-invalid @enderror" name="epochs" required value="5" step="1" min="1" max="10000">
					@error('epochs')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				{{-- Batch size field --}}
				<div class="col-md text-md-center">
					<label for="batch_size" class="col-form-label align-self-center font-weight-bold">{{ __('Batch size') }}</label><br>
					<input id="batch_size" type="number" class="col-md-10 mx-md-auto form-control @error('batch_size') is-invalid @enderror" name="batch_size" required value="8" step="1" min="1" max="10000">
					@error('batch_size')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </div>
				
				{{-- Validation split field --}}
                <div class="col-md text-md-center">
                    <label for="validation_split" class="col-form-label align-self-center font-weight-bold">{{ __('Validation split') }}</label><br>
                    <input id="validation_split" type="number" class="col-md-10 mx-md-auto form-control @error('validation_split') is-invalid @enderror" name="validation_split" required value="0.0" step="0.01" min="0.0" max="0.99">
                    @error('validation_split')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
			</div>
		
			{{-- Save best only --}}
			<div class="form-check row mt-5 px-5">
				<div class="col-md">
					<div class="row">
						<div class="text-md-center">
							<span class="font-weight-bold d-block">During training, do you want to save the best model only?</span>
							<span class="text-md-left float-md-left my-1 d-block">
								<span class="font-weight-bold bg-warning text-dark px-1">NOTE:</span><u class="mx-1">It will work only if Validation Split > 0 </u><br>
								<span class="font-weight-bold bg-warning text-dark px-1">NOTE:</span><u class="mx-1">If you stop and then resume the training, it will resume from the last saved model (that could be a very old checkpoint)</u>
							</span>
						</div>
					</div>
					<div class="row my-2">
						<div class="col text-right pr-4">
							<input class="form-check-input @error('save_best') is-invalid @enderror" type="radio" id="save_best_yes" name="save_best" value="1">
							<label class="form-check-label" for="save_best_yes">Yes</label>
						</div>
						<div class="col text-left pl-4">
							<input class="form-check-input @error('save_best') is-invalid @enderror" type="radio" id="save_best_no" name="save_best" value="0" checked>
							<label class="form-check-label" for="save_best_no">No</label>
						</div>
					</div>
					@error('save_best')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Submit button --}}
			<div class="form-group row my-4">
				<div class="col text-center">
					<button type="submit" class="btn btn-info">
						<i class="fas fa-tools fa-lg mr-2"></i>{{ __('Make Training') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection
