@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container text-center">
    @if($user->id != Auth::user()->id)
        <h2 class="mb-4">Models | user: {{$user->username}}</h2>
    @else
        @if($user->available_space > 0)
            <a href="{{route("networks.create", ['user' => $user])}}">
                <button class="btn btn-info"><strong>+</strong> Build new Model</button>
            </a>
        @endif
        <h2 class="mb-3 mt-3 text-left">Your models</h2>
    @endif

    <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
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
        <div class="row border border-secondary text-center">
            <div class="col-md-2 align-self-center">{{$model->model_name}}</div>
            <div class="col-md-1 align-self-center">{{$model->input_shape}}</div>
            <div class="col-md-1 align-self-center">{{$model->output_classes}}</div>
            <div class="col-md-1 align-self-center">
                @if($model->is_trained && $model->accuracy != NULL) 
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
                <a href="{{ route('networks.show', ['user' => $user, 'network' => $model]) }}">
                    <button class="btn btn-primary">Details</button>
                </a>
                
                {{-- COMPILE BUTTON --}}
                <a href="{{ route("compilations.create", ['user' => $user, 'network' => $model]) }}">
                    <button class="btn btn-warning">Compile</button>
                </a>

                {{-- DOWNLOAD BUTTON --}}
                <a href="{{ route("networks.download", ['user' => $user, 'network' => $model]) }}">
                    <button class="btn btn-outline-dark">Download</button>
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
