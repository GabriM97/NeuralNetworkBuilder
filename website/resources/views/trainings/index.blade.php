@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container-fluid col-md-11 text-center">
    <div class="row">
        <div class="col-md-2 h5">
            <a class="text-decoration-none rounded text-white p-2" href="{{route("networks.index", compact("user"))}}">
                <i class="fas fa-arrow-circle-left mr-2"></i>Models
            </a>
        </div>
    </div>

    @if($user->id != Auth::user()->id)
        <h2 class="mb-4">Trainings | user: {{$user->username}}</h2>
    @else
        @if($user->available_space > 0)
            <a href="{{route("trainings.create", ['user' => $user])}}">
                <button class="btn btn-info"><i class="fas fa-tools mr-2"></i>START NEW TRAINING</button>
            </a>
        @endif
        <h2 class="mb-3 mt-3 text-left">Your trainings</h2>
    @endif

    <div class="main-container rounded p-1 my-2">
        <div class="row text-center font-weight-bold">    <!-- TITLE ROW -->
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

            <div class="row text-center my-3">
                <div class="col-md-2 align-self-center font-weight-bold">
                    @if ($model)
                        <a href="{{route("networks.show", ['user' => $user, 'network' => $model])}}">
                            {{ $model->model_name }}
                        </a>
                    @else
                        <span class="font-weight-bold text-danger">MODEL NOT FOUND</span>
                    @endif
                </div>
                <div class="col-md-2 align-self-center font-weight-bold">
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
                    <a href="{{ route('trainings.show', ['user' => $user, 'training' => $train]) }}" class="text-decoration-none" title="Details">
                        <button class="btn btn-primary btn-circle"><i class="fas fa-list-ul"></i></button>
                    </a>

                    {{-- START/STOP BUTTON --}}
                    @php
                        if($train->status == "started" || $train->status == "paused"){
                            $action = route('trainings.stop', ["user" => $user, "training" => $train]);
                            $method = "stop";
                            $icon = "fa-stop";
                        }else{
                            $action = route('trainings.start', ["user" => $user, "training" => $train]);
                            $method = "start";
                            $icon = "fa-play";
                        }
                    @endphp    
                    <form method="POST" action="{{ $action }}" class="d-inline-block">
                        @csrf
                        <input type="hidden" name="_type" value="{{ $method }}">
                        @php
                            $btn_satatus = NULL;

                            if($train->status == "stopped" && 
                                ($train->in_queue || !$model || !$training_dataset || 
                                    ($train->is_evaluated && !isset($test_dataset)))
                            ){
                                $btn_satatus = "disabled";
                            }
                            else
                                if($train->status == "error")
                                    $btn_satatus = "disabled";
                        @endphp
                        <button class="btn btn-warning btn-circle" title="{{ ucfirst($method) }}" {{ $btn_satatus }}>
                                <i class="fas {{$icon}}"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
