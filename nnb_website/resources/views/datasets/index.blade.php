@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container-fluid col-md-11 text-center">
    <div class="row">
        @php
            if(Auth::user()->rank == -1 && $user->id != Auth::user()->id)
                $visibility = "d-none";
            else
                $visibility = "";
        @endphp
        <div class="col h5">
            <a class="text-decoration-none rounded text-white p-2 {{$visibility}}" href="{{route("home")}}">
                <i class="fas fa-arrow-circle-left mr-2"></i>Dashboard
            </a>
        </div>
        <div class="col offset-md-8 h5">
            <a class="text-decoration-none rounded text-white p-2" href="{{route("networks.index", compact("user"))}}">
                Models<i class="fas fa-arrow-circle-right ml-2"></i>
            </a>
        </div>
    </div>

    @php
        $size = $user->get_tot_files_size();
        if($size/1024 < 1000) 
            $size_render = round($size/1024, 2)." KB ";
        elseif($size/1048576 < 1000) 
            $size_render = round($size/1048576, 2)." MB ";
        else //if($size/1073741824 < 1000) 
            $size_render = round($size/1073741824, 2)." GB ";

        $max_space = round($user->get_max_available_space()/1073741824, 2)." GB";
    @endphp

    @if($user->id != Auth::user()->id)
        <h2>Datasets | user: {{$user->username}}</h2>
        <span class="position-relative float-left">Storage: {{$size_render}} of {{$max_space}} used</span><br>
    @else
        @if($user->available_space > 0)
            <a href="{{route("datasets.create", ['user' => $user])}}">
                <button class="btn btn-info"><i class="fas fa-upload mr-2"></i>IMPORT NEW DATASET</button>
            </a>
        @endif
        <h2 class="mb-3 mt-3 text-left">Your datasets</h2>
    @endif

    <div class="main-container border-left-primary rounded p-1 my-2">
        <div class="row text-center font-weight-bold">    <!-- TITLE ROW -->
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
            <div class="row text-center text-break my-3">
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
                <div class="col-md-1 align-self-center">{{strtoupper($data->file_extension)}}</div>
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
                    <a href="{{ route('datasets.show', ['user' => $user, 'dataset' => $data]) }}" class="text-decoration-none" title="Details">
                        <button class="btn btn-primary btn-circle"><i class="fas fa-list-ul"></i></button>
                    </a>

                    {{-- EDIT BUTTON --}}         
                    <a href="{{ route('datasets.edit', ['user' => $user, 'dataset' => $data]) }}" class="text-decoration-none" title="Edit">
                        <button class="btn btn-light btn-circle"><i class="fas fa-pen"></i></button>
                    </a>

                    {{-- DOWNLOAD BUTTON --}}
                    <a href="{{ route("datasets.download", ['user' => $user, 'dataset' => $data]) }}" class="text-decoration-none" title="Download">
                        <button class="btn btn-warning btn-circle"><i class="fas fa-download"></i></button>
                    </a>

                    {{-- DELETE BUTTON --}}
                    <form class="form-delete d-inline" method="POST" action="{{route('datasets.destroy', ['user' => $user, 'dataset' => $data])}}">
                        @csrf
                        @method("DELETE")
                        <button class="btn btn-danger btn-circle" type="submit" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
