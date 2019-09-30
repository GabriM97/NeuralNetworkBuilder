{{-- ADMIN ONLY --}}

@extends('layout')

@section('page-title', $title)

@section('content')
	<h2>Create new project</h2>
	<form method="POST" action="/projects">
		{{ csrf_field() }}
		Title <input type="text" name="title"><br><br>
		Description <br> <textarea name="description" cols="50" rows="10"></textarea>
		<br><br>
		<button type="submit">Create</button>
		<button type="reset">Reset</button>
	</form>
@endsection