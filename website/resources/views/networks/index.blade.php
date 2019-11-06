@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container-fluid col-md-11 text-center">
    <div class="row">
        <div class="col h5">
            <a class="text-decoration-none rounded text-white p-2" href="{{route("datasets.index", compact("user"))}}">
                <i class="fas fa-arrow-circle-left mr-2"></i>Datasets
            </a>
        </div>
        <div class="col offset-md-8 h5">
            <a class="text-decoration-none rounded text-white p-2" href="{{route("trainings.index", compact("user"))}}">
                Trainings<i class="fas fa-arrow-circle-right ml-2"></i>
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
        <h2>Models | user: {{$user->username}}</h2>
        <span class="position-relative float-left">Storage: {{$size_render}} of {{$max_space}} used</span><br>
    @else
        @if($user->available_space > 0)
            <a href="{{route("networks.create", ['user' => $user])}}">
                <button class="btn btn-info"><i class="fas fa-project-diagram mr-2"></i>BUILD NEW MODEL</button>
            </a>
        @endif
        <h2 class="mb-3 mt-3 text-left">Your models</h2>
    @endif

    <div class="main-container rounded p-1 my-2">
        <div class="row text-center font-weight-bold">    <!-- TITLE ROW -->
            <div class="col-md-2 align-self-center">Name</div>
            <div class="col-md-1 align-self-center">Input shape</div>
            <div class="col-md-1 align-self-center">Output classes</div>
            <div class="col-md-1 align-self-center">Accuracy</div>
            <div class="col-md-1 align-self-center">Compiled</div>
            <div class="col-md-1 align-self-center">Trained</div>
            <div class="col-md-1 align-self-center pl-1 pr-1">Last time used</div>
            <div class="col-md-1 align-self-center pl-1 pr-1">Upload Date</div>
            <div class="col-md-3 align-self-center">Action</div>
        </div>

        @foreach ($networks as $model)
            <div class="row text-center text-break my-3">
                <div class="col-md-2 align-self-center">{{$model->model_name}}</div>
                <div class="col-md-1 align-self-center">{{$model->input_shape}}</div>
                <div class="col-md-1 align-self-center">{{$model->output_classes}}</div>
                <div class="col-md-1 align-self-center">
                    @if($model->is_trained && $model->accuracy !== NULL) 
                        {{$model->accuracy*100}}%
                    @else
                        <span class="font-italic">Not trained yet</span>
                    @endif
                </div>
                <div class="col-md-1 align-self-center">@if($model->is_compiled) Yes @else No @endif</div>
                <div class="col-md-1 align-self-center">@if($model->is_trained) Yes @else No @endif</div>
                <div class="col-md-1 align-self-center pl-1 pr-1">  {{-- LAST TIME USED --}}
                    @php
                        if($model->last_time_used)
                            echo $model->last_time_used;
                        else
                            echo "Never";
                    @endphp
                </div>
                <div class="col-md-1 align-self-center pl-1 pr-1">{{$model->created_at}}</div>

                <div class="col-md-3 align-self-center">
                    {{-- DETAILS BUTTON --}}
                    <a href="{{ route('networks.show', ['user' => $user, 'network' => $model]) }}" class="text-decoration-none" title="Details">
                        <button class="btn btn-primary btn-circle"><i class="fas fa-list-ul"></i></button>
                    </a>
                    
                    {{-- EDIT BUTTON --}}         
                    <a href="{{ route('networks.edit', ['user' => $user, 'network' => $model]) }}" class="text-decoration-none" title="Edit">
                        <button class="btn btn-light btn-circle"><i class="fas fa-pen"></i></button>
                    </a>

                    {{-- COMPILE BUTTON --}}
                    <a href="{{ route("compilations.create", ['user' => $user, 'network' => $model]) }}" class="text-decoration-none" title="Compile">
                        <button class="btn btn-success btn-circle text-dark"><i class="fas fa-barcode fa-lg"></i></button>
                    </a>

                    {{-- DOWNLOAD BUTTON --}}
                    <a href="{{ route("networks.download", ['user' => $user, 'network' => $model]) }}" class="text-decoration-none" title="Download">
                        <button class="btn btn-warning btn-circle"><i class="fas fa-download"></i></button>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
