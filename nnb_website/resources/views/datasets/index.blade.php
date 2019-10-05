@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container text-center">
    @if(Auth::user()->rank == -1 && $user->id != Auth::user()->id)
        <h2 class="mb-4">Datasets | user: {{$user->username}}</h2>
    @else
        <a href="{{route("datasets.create", ['user' => $user])}}">
            <button class="btn btn-info"><strong>+</strong> Import new Dataset</button>
        </a>
        <h2 class="mb-3 text-left">Your datasets</h2>
    @endif

    <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
        <div class="col-md-2 align-self-center">Title</div>
        <div class="col-md-1 align-self-center">Filesize</div>
        <div class="col-md-1 align-self-center">File type</div>
        <div class="col-md-1 align-self-center">Input shape</div>
        <div class="col-md-1 align-self-center">Output classes</div>
        <div class="col-md-1 align-self-center">Dataset type</div>
        <div class="col-md-1 align-self-center pl-1 pr-1">Last time used</div>
        <div class="col-md-1 align-self-center pl-1 pr-1">Upload Date</div>
        <div class="col-md-3 align-self-center">Action</div>
    </div>

    @foreach ($datasets as $data)
        <div class="row border border-secondary text-center">
            <div class="col-md-2 align-self-center">{{$data->data_name}}</div>
            <div class="col-md-1 align-self-center">    {{-- FILE SIZE --}}
                @php
                    if($data->file_size/1024 < 1000) 
                        echo round($data->file_size/1024, 2)." KB";
                    elseif($data->file_size/1048576 < 1000) 
                        echo round($data->file_size/1048576, 2)." MB";
                    else //if($data->file_size/1073741824 < 1000) 
                        echo round($data->file_size/1073741824, 2)." GB";
                @endphp
            </div>
            <div class="col-md-1 align-self-center">{{$data->file_extension}}</div>
            <div class="col-md-1 align-self-center">{{$data->x_shape}}</div>
            <div class="col-md-1 align-self-center">{{$data->y_classes}}</div>
            <div class="col-md-1 align-self-center">    {{-- DATA TYPE --}}
                @php
                    if($data->is_train) echo "Train";
                    if($data->is_test) echo "Test";
                    if($data->is_generic) echo "Generic";
                @endphp
            </div>
            <div class="col-md-1 align-self-center pl-1 pr-1">
                @php
                    if($data->last_time_used)
                        echo $data->last_time_used;
                    else
                        echo "Never";
                @endphp
            </div>
            <div class="col-md-1 align-self-center pl-1 pr-1">{{$data->created_at}}</div>

            <div class="col-md-3 align-self-center">
                {{-- DETAILS BUTTON --}}
                <a href="{{ route('datasets.show', ['user' => $user, 'dataset' => $data]) }}">
                    <button class="btn btn-primary">Details</button>
                </a>
                
                {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('datasets.destroy', ['user' => $user, 'dataset' => $data])}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>

                {{-- DOWNLOAD BUTTON DOESN'T WORK--}}
                <a href="{{ route("datasets.download", ['user' => $user, 'dataset' => $data]) }}">
                    <button class="btn btn-outline-dark">Download</button>
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
