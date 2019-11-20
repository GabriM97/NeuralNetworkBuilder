{{-- ADMIN ONLY --}}
@if(Auth::user()->rank != -1)
    {!! redirect(route("home"))  !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md h5">
				<a class="text-decoration-none rounded text-white p-md-2" href="{{route("nodes.index")}}">
					<i class="fas fa-arrow-circle-left mr-2"></i>Nodes
				</a>
			</div>
		</div>
	</div>

	<form method="POST" action="{{route("nodes.store")}}">
		@csrf
		<div class="main-container rounded container col-md-5 p-2">
			<h2 class="mb-5 mt-3 text-center">Create new Node<i class="fas fa-network-wired fa-xs pl-3"></i></h2>

			{{-- IP Address field --}}
			<div class="form-group row px-5">
				<label for="ip_address" class="col-md-3 col-form-label text-md-right">{{ __('IP Address') }}</label>

				<div class="col-md">
					<input id="ip_address" type="text" class="form-control @error('ip_address') is-invalid @enderror" name="ip_address" value="{{ old('ip_address') }}" required autofocus>

					@error('ip_address')
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
					<textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Insert node description">{{ old('description') }}</textarea>

					@error('description')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Submit button --}}
			<div class="form-group row mb-0">
				<div class="col-md text-center">
					<button type="submit" class="btn btn-info">
						<i class="fas fa-plus fa-xs mr-0"></i>
						<i class="fas fa-network-wired fa-lg mr-2"></i>
						{{ __('Add Node') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection