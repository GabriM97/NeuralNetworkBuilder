{{-- ADMIN ONLY --}}

@extends("layout")

@section('page-title', $title)

@section('content')
<br><br>
<a href="/projects/create"><button>Create new project</button></a>

<br><br>
<h2>Our Project</h2>
<ul>
    @foreach ($projects as $proj)
    <li>
        <a href="/projects/{{ $proj->id }}">{{ $proj->title }}</a>
        &emsp; <a href="/projects/{{ $proj->id }}/edit"><button>Edit</button></a>
        <form class="form-delete" method="POST" action="/projects/{{ $proj->id }}">
            @csrf
            @method("DELETE")
            <button>Delete</button>
        </form>
    </li>
    <br><br>
    @endforeach
</ul>

@endsection
