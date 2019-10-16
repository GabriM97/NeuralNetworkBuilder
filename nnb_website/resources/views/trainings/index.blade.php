@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container text-center">
    @if($user->id != Auth::user()->id)
        <h2 class="mb-4">Trainings | user: {{$user->username}}</h2>
    @else
        @if($user->available_space > 0)
            <a href="{{route("trainings.create", ['user' => $user])}}">
                <button class="btn btn-info"><strong>+</strong> Start new training</button>
            </a>
        @endif
        <h2 class="mb-3 mt-3 text-left">Your trainings</h2>
    @endif

    <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
        <div class="col-md-2 align-self-center">Model</div>
        <div class="col-md-2 align-self-center">Training Dataset</div>
        <div class="col-md-2 align-self-center">Test Dataset</div>
        <div class="col-md-1 align-self-center">Epochs</div>
        <div class="col-md-1 align-self-center">Batch size</div>
        <div class="col-md-1 align-self-center">Validation split</div>
        <div class="col-md-1 align-self-center">Train status</div>
        <div class="col-md-2 align-self-center">Last start</div>
    </div>

    @foreach ($trainings as $train)
        <div class="row border border-secondary text-center">
            <div class="col-md-2 align-self-center">{{ Network::where("id", $train->model_id)->get()->model_name }}</div>
            <div class="col-md-2 align-self-center">{{ Dataset::where("id", $train->dataset_id_training)->get()->data_name }}</div>
            <div class="col-md-2 align-self-center">{{ Dataset::where("id", $train->dataset_id_test)->get()->data_name }}</div>
            <div class="col-md-1 align-self-center">{{ $train->epochs }}</div>
            <div class="col-md-1 align-self-center">{{ $train->batch_size }}</div>
            <div class="col-md-1 align-self-center">{{ $train->validation_split }}</div>
            <div class="col-md-1 align-self-center">{{ $train->training_status }}</div>
            <div class="col-md-2 align-self-center">{{ $train->updated_at }}</div>

            {{--
            <div class="col-md-3 align-self-center">
                {{-- DETAILS BUTTON -}}
                <a href="{{ route('networks.show', ['user' => $user, 'network' => $train]) }}">
                    <button class="btn btn-primary">Details</button>
                </a>
                
                {{-- COMPILE BUTTON -}}
                <a href="{{ route("compilations.create", ['user' => $user, 'network' => $train]) }}">
                    <button class="btn btn-warning">Compile</button>
                </a>

                {{-- DOWNLOAD BUTTON -}}
                <a href="{{ route("networks.download", ['user' => $user, 'network' => $train]) }}">
                    <button class="btn btn-outline-dark">Download</button>
                </a>
            </div>
            --}}
        </div>
    @endforeach
</div>
@endsection
