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
				<a class="text-decoration-none rounded text-white p-md-2" href="{{route("users.index")}}">
					<i class="fas fa-arrow-circle-left mr-2"></i>Users
				</a>
			</div>
		</div>
	</div>

	<form method="POST" action="{{route("users.store")}}">
		@csrf
		<div class="main-container rounded container col-md-5 p-2">
			<h2 class="mb-5 mt-3 text-center">Create new User<i class="fas fa-user fa-xs pl-3"></i></h2>
			
			{{-- Username field --}}
			<div class="form-group row px-5">
				<label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

				<div class="col-md-6">
					<input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

					@error('username')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Email field --}}
			<div class="form-group row px-5">
				<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

				<div class="col-md-6">
					<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

					@error('email')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

			{{-- Password field --}}
			<div class="form-group row px-5">
				<label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

				<div class="col-md-6">
					<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

					@error('password')
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
						<i class="fas fa-user-plus fa-lg mr-2"></i>{{ __('Add User') }}
					</button>
				</div>
			</div>
		</div>
	</form>
@endsection