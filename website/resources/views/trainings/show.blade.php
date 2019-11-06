@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', "Training | NeuralNetworkBuilder")

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="{{ asset('js/update_realtime_data.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-2 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("trainings.index", compact("user"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Trainings
                </a>
            </div>
        </div>
    </div>

    @if($training->return_message)  <!-- Info message box -->
        @php
            if($training->status == 'error') $msg_class = "alert-danger";
            if($training->status == 'started' || $training->status == 'paused') $msg_class = "alert-warning";
            if($training->status == 'stopped' && $training->training_percentage >= 1) $msg_class = "alert-success";
            if($training->status == 'stopped' && $training->training_percentage < 1) $msg_class = "alert-primary";
            //else only ? (instead of last if)
        @endphp
    
        <div id="return-alert" class="container text-center alert {{$msg_class}} mt-2" role="alert">{{$training->return_message}}</div>
        
        @php
            if($training->status != 'started' && $training->status != 'error' && $training->status != 'paused'){
                $training->return_message = NULL;
                $training->update();
            }
        @endphp
    @endif

    <div class="main-container rounded container col-md-8 text-md-center p-2 my-4">
        <div class="align-self-center text-center my-3">
            <div class="d-inline-block align-self-center text-right"> {{-- TRAINING DETAILS --}}
                <h2 class="content-title m-0"><i class="fas fa-tools fa-xs pr-2"></i>Training details</h2>
            </div>
            
            {{-- START/STOP BUTTON --}}
            <div class="d-inline-block align-self-center text-left">   
                @php
                    if($training->status == "started" || $training->status == "paused"){
                        $action = route('trainings.stop', compact("user", "training"));
                        $method = "stop";
                        $icon = "fa-stop";
                    }else{
                        $action = route('trainings.start', compact("user", "training"));
                        $method = "start";
                        $icon = "fa-play";
                    }
                @endphp    
                <form class="training-main-form" method="POST" action="{{ $action }}">
                    @csrf
                    <input type="hidden" name="_type" value="{{ $method }}">
                    @php
                        $btn_satatus = NULL;

                        if($training->status == "stopped" && 
                            ($training->in_queue || !$network || !$dataset_train || 
                                ($training->is_evaluated && !isset($dataset_test)))
                        ){
                            $btn_satatus = "disabled";
                        }
                        else
                            if($training->status == "error")
                                $btn_satatus = "disabled";
                    @endphp
                    <button class="btn btn-warning" {{ $btn_satatus }}>
                        {{ ucfirst($method) }}<i class="fas {{$icon}} fa-lg pl-2"></i>
                    </button>
                </form>
            </div>
            
            {{-- PAUSE/RESUME BUTTON --}}
            <div class="d-inline-block text-left mb-3">       
                @php
                    if($training->status == "paused"){
                        $action = route('trainings.resume', compact("user", "training"));
                        $method = "resume";
                        $icon = "fa-play";
                    }else{
                        $action = route('trainings.pause', compact("user", "training"));
                        $method = "pause";
                        $icon = "fa-pause";
                    }
                @endphp
                <form class="training-main-form" method="POST" action="{{ $action }}">
                    @csrf
                    <input type="hidden" name="_type" value="{{ $method }}">
                    @php
                        if(($training->status != 'paused' && $training->status != 'started') || ($training->in_queue && $training->status == 'paused') || $training->evaluation_in_progress){
                            $btn_satatus = "disabled";
                            $class = "d-none";
                        }else{
                            $btn_satatus = NULL;
                            $class = NULL;
                        }
                    @endphp
                    <button id="pause-resume-btn" class="btn btn-info {{ $class }}" {{ $btn_satatus }}>
                        {{ ucfirst($method) }}<i class="fas {{$icon}} fa-lg pl-2"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row px-5">
            <div class="col-md">
                <div class="row my-3">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Description</div>
                    <div class="col-md align-self-center text-md-left">
                        @if($training->train_description)     {{-- description != NULL --}}
                            {{$training->train_description}}
                        @else
                            <span class="font-italic">No description</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-100"></div> {{-- break to a new line --}}

            <!-- Left column -->
            <div class="col-md">
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Epochs</div>
                    <div id="epochs" class="col-md-4 align-self-center text-md-left">{{ $training->epochs }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Batch size</div>
                    <div class="col-md-4 align-self-center text-md-left">{{ $training->batch_size }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Validation split</div>
                    <div class="col-md-4 align-self-center text-md-left">{{ $training->validation_split*100 }} %</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Model evaluation</div>
                    <div class="col-md-4 align-self-center text-md-left">{{ $training->is_evaluated ? "Yes" : "No" }}</div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-md">
                @csrf
                <div id="in_queue" value="{{$training->in_queue}}" style="display: none"></div>
                <div class="row my-2">
                    <div class="col-md-5 align-self-center text-md-right font-weight-bold">Training status</div>
                    <div class="col-md-7 align-self-center text-md-left font-weight-bold">
                        @if ($training->status == 'started')
                            <span class="text-primary">
                                <i class="fas fa-spinner fa-spin fa-lg pr-2"></i>
                                <span id="train_status" value="{{$training->status}}">In Progress</span> | 
                                <span id="train_perc">{{$training->training_percentage*100}}</span>%
                            </span>

                        @elseif ($training->status == 'paused')
                            <span class="text-info">
                                <i class="fas fa-pause fa-lg pr-2"></i>
                                <span id="train_status" value="{{$training->status}}">In Pause</span> | 
                                <span id="train_perc">{{$training->training_percentage*100}}</span>%
                            </span>

                        @elseif ($training->status == 'stopped' && $training->training_percentage >= 1)  {{-- training completed --}}
                            <span class="text-success">
                                <i class="fas fa-check-circle fa-lg pr-2"></i>
                                <span id="train_status" value="{{$training->status}}">Completed</span> | 
                                <span id="train_perc">{{$training->training_percentage*100}}</span>%
                            </span>
                        
                        @elseif ($training->status == 'error')
                            <span class="text-danger" value="{{$training->status}}">
                                <i class="fas fa-times fa-lg pr-2"></i>
                                <span id="train_status">ERROR</span> 
                            </span>
                        
                        @else
                            <span class="text-secondary">
                                <i class="fas fa-stop fa-lg pr-2"></i>
                                <span id="train_status" value="{{$training->status}}">Not in progress</span> | 
                                <span id="train_perc">{{$training->training_percentage*100}}</span>%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-5 align-self-center text-md-right font-weight-bold">Save best only model</div>
                    <div class="col-md-7 align-self-center text-md-left">{{ $training->save_best_only ? "Yes" : "No" }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-5 align-self-center text-md-right font-weight-bold">Created at</div>
                    <div class="col-md-7 align-self-center text-md-left">{{ $training->created_at }}</div>
                </div>
            </div>
                
            <div class="w-100"></div>
            <!-- Chart -->
            <div class="col text-center {{($training->status != "started") ? "d-none" : NULL}}">
                <div id="chart-container">
                    <canvas id="training_chart" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

        <div class="row px-5 mt-2">
            <div class="col-6 text-right">   {{-- EDIT BUTTON --}}         
                <a href="{{ route('trainings.edit', compact("user", "training")) }}">
                    @php
                        if($training->in_queue || $training->status == "paused")
                            $btn_satatus = "disabled";
                        else
                            $btn_satatus = NULL;
                    @endphp
                    <button class="btn btn-light" {{ $btn_satatus }}><i class="fas fa-pen fa-lg mr-2"></i>Edit</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('trainings.destroy', compact("user", "training"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit" {{ $btn_satatus }}><i class="fas fa-trash-alt fa-lg mr-2"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-8 text-md-center p-2 my-4">
        {{-- MODEL DETAILS --}}
        <div class="align-self-center text-center my-3">
            <div class="d-inline-block align-self-center text-right">
                <h2 class="content-title m-0"><i class="fas fa-project-diagram fa-xs pr-2"></i>Model details</h2>
            </div>

            {{-- DOWNLOAD BUTTON --}}
            <div class="d-inline-block align-self-center text-left">
                <a href="{{ route("networks.download", compact('user', 'network')) }}">
                    <button class="btn btn-sm btn-warning">Download<i class="fas fa-download pl-2"></i></button>
                </a>
            </div>
        </div>

        <div class="row px-5">
        @if ($network)
            <!-- Left column -->
            <div class="col-md">
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Title</div>
                    <div class="col-md-4 align-self-center text-md-left">
                        <a href="{{route("networks.show", compact("user", "network"))}}">
                            {{ $network->model_name }}
                        </a>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Description</div>
                    <div class="col-md-4 align-self-center text-md-left">
                        @if($network->model_description)     {{-- description != NULL --}}
                            {{$network->model_description}}
                        @else
                            <span class="font-italic">No description</span>
                        @endif
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Input shape</div>
                    <div class="col-md-4 align-self-center text-md-left">{{$network->input_shape}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-8 align-self-center text-md-right font-weight-bold">Output classes</div>
                    <div class="col-md-4 align-self-center text-md-left">{{$network->output_classes}}</div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-md text-break">
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Learning rate</div>
                    <div class="col-md-8 align-self-center text-md-left">
                        {{ App\Compilation::where("model_id", $network->id)->first()->learning_rate }}
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Optimizer</div>
                    <div class="col-md-8 align-self-center text-md-left">
                        {{ App\Compilation::where("model_id", $network->id)->first()->optimizer }}
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Is trained</div>
                    <div class="col-md-8 align-self-center text-md-left">
                        {{ $network->is_trained ? "Yes" : "No" }}
                    </div>
                </div>
                @if ($network->is_trained && $network->accuracy !== NULL && $network->loss !== NULL)
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Accuracy</div>
                        <div class="col-md-8 align-self-center text-md-left"><span id="acc_val">{{$network->accuracy*100}}</span>%</div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Loss</div>
                        <div class="col-md-8 align-self-center text-md-left"><span id="loss_val">{{$network->loss*100}}</span>%</div>
                    </div>
                @endif
            </div>
        @else
            <div class="col my-2 font-weight-bold text-white text-center">
                <span class="bg-danger">
                    <i class="fas fa-times fa-lg"></i>
                    <i class="fas fa-times fa-lg"></i>
                    <i class="fas fa-times fa-lg"></i>
                    <br> MODEL NOT FOUND <br>
                    <i class="fas fa-times fa-lg"></i>
                    <i class="fas fa-times fa-lg"></i>
                    <i class="fas fa-times fa-lg"></i>
                </span>
            </div>
        @endif
        </div>
    </div>
        
    <div class="main-container rounded container col-md-8 text-md-center p-2 my-4">
        {{-- DATASET DETAILS --}}
        <div class="align-self-center text-center my-3">
            <div class="d-inline-block align-self-center text-right">
                <h2 class="content-title m-0"><i class="fas fa-list fa-xs pr-2"></i>Dataset details</h2>
            </div>
        </div>

        <div class="row px-5">
            <!-- Left column -->
            <div class="col-md mb-4">
                <div class="row mt-3 mb-md-3">  {{-- TRAINING DATASET DETAILS --}}
                    <div class="col-md h4 align-self-center text-md-center">Training Data</div>
                </div>

                @if ($dataset_train)
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Title</div>
                        <div class="col-md-8 align-self-center text-md-left">
                            <a href="{{route("datasets.show", ['user' => $user, 'dataset' => $dataset_train])}}">      
                                {{ $dataset_train->data_name }}
                            </a>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Description</div>
                        <div class="col-md-8 align-self-center text-md-left">
                            @if($dataset_train->data_description)     {{-- description != NULL --}}
                                {{ $dataset_train->data_description }}
                            @else
                                <span class="font-italic">No description</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="row my-3">
                        <div class="col font-weight-bold text-white text-center">
                            <span class="bg-danger">
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <br> DATASET NOT FOUND <br>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right column -->
            <div class="col-md">
                <div class="row mt-3 mb-md-3">  {{-- TEST DATASET DETAILS --}}
                    <div class="col-md h4 align-self-center text-md-center">Test Data</div>
                </div>

                @if (isset($dataset_test))
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Title</div>
                        <div class="col-md-8 align-self-center text-md-left">
                            <a href="{{route("datasets.show", ['user' => $user, 'dataset' => $dataset_test])}}">
                                {{ $dataset_test->data_name }}
                            </a>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Description</div>
                        <div class="col-md-8 align-self-center text-md-left">
                            @if($dataset_test->data_description)     {{-- description != NULL --}}
                                {{ $dataset_test->data_description }}
                            @else
                                <span class="font-italic">No description</span>
                            @endif
                        </div>
                    </div>
                @elseif (!$training->is_evaluated)
                    <div class="row my-3">
                        <div class="col align-self-center text-center font-weight-bold">Training not evaluated.</div>
                    </div>
                @else
                    <div class="row my-3">
                        <div class="col font-weight-bold text-white text-center">
                            <span class="bg-danger">
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <br> DATASET NOT FOUND <br>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                                <i class="fas fa-times fa-lg"></i>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection



