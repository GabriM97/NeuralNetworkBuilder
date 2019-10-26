@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', "Training | NeuralNetworkBuilder")

@section('content')

    @if($training->return_message)  <!-- Info message box -->
        @php
            if($training->status == 'error') $msg_class = "alert-danger";
            if($training->status == 'started' || $training->status == 'paused') $msg_class = "alert-warning";
            if($training->status == 'stopped' && $training->training_percentage >= 1) $msg_class = "alert-success";
            if($training->status == 'stopped' && $training->training_percentage < 1) $msg_class = "alert-primary";
            //else only ? (instead of last if)
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$training->return_message}}</div>
        
        @php
            if($training->status != 'started' && $training->status != 'error'){
                $training->return_message = NULL;
                $training->update();
            }
        @endphp

    @endif

    <div class="container col-8 text-sm-center">
        <div class="row my-5">
            <div class="col align-self-center text-center"> {{-- TRAINING DETAILS --}}
                <h2 class="content-title mt-0 mb-2">Training details</h2>
            </div>
            
            <div class="w-100"></div> {{-- break to a new line --}}

            <div class="col">
                <div class="row my-3">
                    <div class="col-3 offset-2 align-self-center text-right font-weight-bold">Description</div>
                    <div class="col-5 align-self-center border text-left">
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
            <div class="col-3 offset-3">
                <div class="row my-2">
                    <div class="col-8 align-self-center text-right font-weight-bold">Epochs</div>
                    <div class="col-4 align-self-center text-left">{{ $training->epochs }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-8 align-self-center text-right font-weight-bold">Batch size</div>
                    <div class="col-4 align-self-center text-left">{{ $training->batch_size }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-8 align-self-center text-right font-weight-bold">Validation split</div>
                    <div class="col-4 align-self-center text-left">{{ $training->validation_split*100 }} %</div>
                </div>
                <div class="row my-2">
                    <div class="col-8 align-self-center text-right font-weight-bold">Model evaluation</div>
                    <div class="col-4 align-self-center text-left">{{ $training->is_evaluated ? "Yes" : "No" }}</div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-5">
                <div class="row my-2">
                    <div class="col-5 align-self-center text-right font-weight-bold">Training status</div>
                    <div class="col-7 align-self-center text-left">
                            @if ($training->status == 'started')
                            <span class="text-primary font-weight-bold">In progress | {{$training->training_percentage*100}}%</span>
                        @elseif ($training->status == 'paused')
                            <span class="text-info font-weight-bold">In Pause | {{$training->training_percentage*100}}%</span>
                        @elseif ($training->status == 'stopped' && $training->training_percentage >= 1)  {{-- training completed --}}
                            <span class="text-success font-weight-bold">Completed | 100%</span>
                        @elseif ($training->status == 'error')
                            <span class="text-danger font-weight-bold">ERROR</span>
                        @else
                            <span class="font-italic">Not in progress</span>
                        @endif
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-5 align-self-center text-right font-weight-bold">Save best only model</div>
                    <div class="col-7 align-self-center text-left">{{ $training->save_best_only ? "Yes" : "No" }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-5 align-self-center text-right font-weight-bold">Created at</div>
                    <div class="col-7 align-self-center text-left">{{ $training->created_at }}</div>
                </div>
            </div>    

        </div>
        
        <div class="row my-5">   {{-- MODEL DETAILS --}}
            <div class="col h4 text-center">Model details</div>
            <div class="w-100"></div> {{-- break to a new line --}}

            @if ($network)
                <!-- Left column -->
                <div class="col-4 offset-2">
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Title</div>
                        <div class="col-8 align-self-center text-left">{{ $network->model_name }}</div>
                    </div>
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Description</div>
                        <div class="col-8 align-self-center border text-left">
                            @if($network->model_description)     {{-- description != NULL --}}
                                {{$network->model_description}}
                            @else
                                <span class="font-italic">No description</span>
                            @endif
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-5 offset-2">
                            <div class="align-self-center text-center font-weight-bold">Input shape</div>
                            <div class="align-self-center text-center">{{$network->input_shape}}</div>
                        </div>
                        <div class="col-5">
                            <div class="align-self-center text-center font-weight-bold">Output classes</div>
                            <div class="align-self-center text-center">{{$network->output_classes}}</div>
                        </div>
                    </div>
                </div>

                <!-- Right column -->
                <div class="col-6 text-break">
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Learning rate</div>
                        <div class="col-8 align-self-center text-left">
                            {{ App\Compilation::where("model_id", $network->id)->first()->learning_rate }}
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Optimizer</div>
                        <div class="col-8 align-self-center text-left">
                            {{ App\Compilation::where("model_id", $network->id)->first()->optimizer }}
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Is trained</div>
                        <div class="col-8 align-self-center text-left">
                            {{ App\Network::find($network->id)->is_trained ? "Yes" : "No"}}
                        </div>
                    </div>
                </div>
            @else
                <div class="col my-2 font-weight-bold text-danger text-center">
                    *************** <br>
                    MODEL NOT FOUND <br>
                    *************** <br>
                </div>
            @endif
        </div>
        
        <div class="row my-5">   {{-- DATASET DETAILS --}}
            <div class="col h4 text-center">Dataset details</div>
            <div class="w-100"></div> {{-- break to a new line --}}
            
            <!-- Left column -->
            <div class="col-4 offset-2">
                <div class="row my-3">  {{-- TRAINING DATASET DETAILS --}}
                    <div class="col h5 align-self-center text-center">Training Data</div>
                </div>

                @if ($dataset_train)
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Title</div>
                        <div class="col-8 align-self-center text-left">{{ $dataset_train->data_name }}</div>
                    </div>
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Description</div>
                        <div class="col-8 align-self-center text-left">
                            @if($dataset_train->data_description)     {{-- description != NULL --}}
                                {{ $dataset_train->data_description }}
                            @else
                                <span class="font-italic">No description</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="row my-3">
                        <div class="col font-weight-bold text-danger text-center">
                            ***************** <br>
                            DATASET NOT FOUND <br>
                            ***************** <br>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right column -->
            <div class="col-4">
                <div class="row my-3">  {{-- TEST DATASET DETAILS --}}
                    <div class="col h5 align-self-center text-center">Test Data</div>
                </div>

                @if (isset($dataset_test))
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Title</div>
                        <div class="col-8 align-self-center text-left">{{ $dataset_test->data_name }}</div>
                    </div>
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Description</div>
                        <div class="col-8 align-self-center text-left">
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
                        <div class="col font-weight-bold text-danger text-center">
                            ***************** <br>
                            DATASET NOT FOUND <br>
                            ***************** <br>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row my-5">
            <div class="col-6 text-right">   {{-- START BUTTON --}}         
                <a href="{{ route('trainings.start', compact("user", "training")) }}">
                    <button class="btn btn-warning" {{ (!$network || !$dataset_train || ($training->is_evaluated && !isset($dataset_test))) ? "disabled" : NULL}}>Start Training</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('trainings.destroy', compact("user", "training"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection



