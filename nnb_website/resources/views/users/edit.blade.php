@extends('layout')

@section('page-title', $title)

@section('content')
    <h2>Edit project {{ $project->id }}</h2>

    <form class="form-edit" method="POST" action="/projects/{{ $project->id }}">
        @csrf
        @method("PATCH")

        Title <input type="text" name="title" value="{{ $project->title }}"><br><br>
        Description <br> <textarea name="description" cols="50" rows="10">{{ $project->description }}</textarea>
        
        <br><br>
        
        <button type="submit">Update</button>
        <button type="reset">Reset</button>
    </form>

    <form class="form-delete" method="POST" action="/projects/{{ $project->id }}">
        @csrf
        @method("DELETE")

        <button type="submit">Delete</button>
    </form>
@endsection
