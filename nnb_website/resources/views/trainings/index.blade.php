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
                @if ($model)
                    <a href="{{route("networks.show", ['user' => $user, 'network' => $model])}}">
                        {{ $model->model_name }}
                    </a>
                @else
                    <span class="font-weight-bold text-danger">MODEL NOT FOUND</span>
                @endif
            </div>
            <div class="col-md-2 align-self-center">
                @if ($training_dataset)
                    <a href="{{route("datasets.show", ['user' => $user, 'dataset' => $training_dataset])}}">    
                        {{ $training_dataset->data_name }}
                    </a>
                @else
                    <span class="font-weight-bold text-danger">DATASET NOT FOUND</span>
                @endif
            </div>
            <div class="col-md-2 align-self-center">
                @if ($test_dataset)
                    <a href="{{route("datasets.show", ['user' => $user, 'dataset' => $test_dataset])}}">
                        {{ $test_dataset->data_name }}
                    </a>
                @elseif (!$train->is_evaluated)
                    <span class="font-italic">Training not evaluated</span>
                @else
                    <span class="font-weight-bold text-danger">DATASET NOT FOUND</span>
                @endif
                
            </div>
            <div class="col-md-1 align-self-center">{{ $train->epochs }}</div>
            <div class="col-md-1 align-self-center">{{ $train->batch_size }}</div>
            <div class="col-md-1 align-self-center">{{ $train->validation_split*100 }}%</div>
            <div class="col-md-1 align-self-center">
                @if ($train->status == 'started')
                    <span class="text-primary font-weight-bold">IN PROGRESS</span>
                @elseif ($train->status == 'paused')
                    <span class="text-info font-weight-bold">IN PAUSE</span>
                @elseif ($train->status == 'stopped' && $train->training_percentage >= 1)  {{-- training completed --}}
                    <span class="text-success font-weight-bold">COMPLETED 100%</span>
                @elseif ($train->status == 'error')
                    <span class="text-danger font-weight-bold">ERROR</span>
                @else
                    <span class="font-italic">Not in progress</span>
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
