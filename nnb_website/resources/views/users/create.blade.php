{{-- ADMIN ONLY --}}
@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')
	<div class="container col-md-5">
		<h2 class="mb-5">Create new User</h2>
		<form method="POST" action="{{route("users.store")}}">
			@csrf
			
			{{-- Username field --}}
			<div class="form-group row">
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
			<div class="form-group row">
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
			<div class="form-group row">
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
				<div class="col-md-6 offset-md-4">
					<button type="submit" class="btn btn-primary">
						{{ __('Add User') }}
					</button>
				</div>
			</div>
		</form>
	</div>
@endsection