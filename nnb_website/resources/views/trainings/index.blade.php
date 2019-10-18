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
        <div class="col-md-2 align-self-center">Action</div>
    </div>

    @foreach ($trainings as $train)
        @php
            $model = App\Network::find($train->model_id);
            $training_dataset = App\Dataset::find($train->dataset_id_training);
            $test_dataset = App\Dataset::find($train->dataset_id_test);
        @endphp

        <div class="row border border-secondary text-center">
            <div class="col-md-2 align-self-center">
                <a href="{{route("networks.show", ['user' => $user, 'network' => $model])}}">
                    {{ $model->model_name }}
                </a>
            </div>
            <div class="col-md-2 align-self-center">
                <a href="{{route("datasets.show", ['user' => $user, 'dataset' => $training_dataset])}}">    
                    {{ $training_dataset->data_name }}
                </a>
            </div>
            <div class="col-md-2 align-self-center">
                @if ($test_dataset)
                    <a href="{{route("networks.show", ['user' => $user, 'dataset' => $test_dataset])}}">
                        {{ $test_dataset->data_name }}
                    </a>
                @else
                    <span class="font-italic">Training not evaluated</span>
                @endif
                
            </div>
            <div class="col-md-1 align-self-center">{{ $train->epochs }}</div>
            <div class="col-md-1 align-self-center">{{ $train->batch_size }}</div>
            <div class="col-md-1 align-self-center">{{ $train->validation_split }}</div>
            <div class="col-md-1 align-self-center">
                @if (!$train->training_status)
                    <span class="font-italic">Not in progress</span>
                @elseif ($train->training_status < 0.99)
                    <span class="font-italic">In progress</span>
                @else
                    <span class="font-italic">Completed</span>
                @endif
            </div>
            <div class="col-md-2 align-self-center">
                {{-- DETAILS BUTTON --}}
                <a href="{{ route('trainings.show', ['user' => $user, 'training' => $train]) }}">
                    <button class="btn btn-primary">Details</button>
                </a>
            </div>

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
