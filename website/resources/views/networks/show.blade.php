@if(Auth::user()->rank != -1 && $user->id != Auth::user()->id)
    {!! redirect(route("home")) !!}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

    {{-- 
    @if(isset($return_status))
        @php
            if($return_status == 0) $msg_class = "alert-success";
            else $msg_class = "alert-danger";
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$return_msg}}</div>
        
    @endif
    --}}
    <div class="container">
        <div class="row">
            <div class="col h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("networks.index", compact("user"))}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Models
                </a>
            </div>
            <div class="col offset-8 h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("trainings.index", compact("user"))}}">
                    Trainings<i class="fas fa-arrow-circle-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="main-container rounded container col-md-8 text-md-center p-2 my-4">
        <div class="align-self-center text-center my-3">
            <div class="d-inline-block align-self-center">
                <h2 class="content-title m-0"><i class="fas fa-project-diagram fa-xs pr-2"></i>Model details</h2>
            </div>

            {{-- DOWNLOAD BUTTON --}}
            <div class="d-inline-block align-self-center text-left">
                <a href="{{ route("networks.download", compact('user', 'network')) }}">
                    <button class="btn btn-sm btn-warning">Download<i class="fas fa-download pl-2"></i></button>
                </a>
            </div>

            <div class="d-inline-block align-self-center text-left">
                @if(!$network->is_compiled)
                    {{-- COMPILE BUTTON --}}
                    <a href="{{ route("compilations.create", compact('user', 'network')) }}">
                        <button class="btn btn-sm btn-success text-dark">Compile<i class="fas fa-barcode fa-lg pl-2"></i></button>
                    </a>
                @endif
            </div>
        </div>
        
        <div class="row px-5">
            <!-- Left column -->
            <div class="col-md">
                <div class="row my-2">
                    <div class="col-md-7 align-self-center text-md-right font-weight-bold">Model type</div>
                    <div class="col-md-5 align-self-center text-md-left">{{$network->model_type}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-7 align-self-center text-md-right font-weight-bold">Input shape</div>
                    <div class="col-md-5 align-self-center text-md-left">{{$network->input_shape}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-7 align-self-center text-md-right font-weight-bold">Output classes</div>
                    <div class="col-md-5 align-self-center text-md-left">{{$network->output_classes}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-7 align-self-center text-md-right font-weight-bold">File size</div>
                    <div class="col-md-5 align-self-center text-md-left pr-0">    {{-- FILE SIZE --}}
                        @php
                            if($network->file_size/1024 < 1000) 
                                echo round($network->file_size/1024, 2)." KB";
                            elseif($network->file_size/1048576 < 1000) 
                                echo round($network->file_size/1048576, 2)." MB";
                            else //if($network->file_size/1073741824 < 1000) 
                                echo round($network->file_size/1073741824, 2)." GB";
                        @endphp
                    </div>
                </div>
                <div class="row my-2">
					<div class="col-md-7 align-self-center text-md-right font-weight-bold">Is trained?</div>
                    <div class="col-md-5 align-self-center text-md-left"> @if ($network->is_trained) Yes @else No @endif</div>
                </div>
				@if ($network->is_trained && $network->accuracy !== NULL && $network->loss !== NULL)
                    <div class="row my-2">
                        <div class="col-md-7 align-self-center text-md-right font-weight-bold">Accuracy</div>
                        <div class="col-md-5 align-self-center text-md-left">{{$network->accuracy*100}}%</div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-7 align-self-center text-md-right font-weight-bold">Loss</div>
                        <div class="col-md-5 align-self-center text-md-left">{{$network->loss*100}}%</div>
                    </div>
                @endif
                <div class="row my-2">
					<div class="col-md-7 align-self-center text-md-right font-weight-bold">Is compiled?</div>
					<div class="col-md-5 align-self-center text-md-left"> @if ($network->is_compiled) Yes @else No @endif</div>
				</div>
            </div>

            <!-- Right column -->
            <div class="col-md text-break">

                @if(Auth::user()->rank == -1 && $user->id != Auth::user()->id)
                    <div class="row my-2">
                        <div class="col-md-4 align-self-center text-md-right font-weight-bold">Owner</div>
                        <div class="col-md-8 align-self-center text-md-left">{{$user->username}}</div>
                    </div>
                @endif

                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Title</div>
                    <div class="col-md-8 align-self-center text-md-left">{{ $network->model_name }}</div>
                </div>
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Description</div>
                    <div class="col-md-8 align-self-center text-md-left">
                        @if($network->model_description)     {{-- description != NULL --}}
                            {{$network->model_description}}
                        @else
                            <span class="font-italic">No description</span>
                        @endif
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Last time used</div>
                    <div class="col-md-8 align-self-center text-md-left">
                        @if($network->last_time_used)
                                {{$network->last_time_used}}
                        @else
                            <span class="font-italic">Never</span>
                        @endif    
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-4 align-self-center text-md-right font-weight-bold">Uploaded at</div>
                    <div class="col-md-8 align-self-center text-md-left">{{$network->created_at}}</div>
				</div>
			</div>            
		</div>
        
        <div class="row my-2 mb-5 px-5">
            <div class="col-6 text-right">   {{-- EDIT BUTTON --}}         
                <a href="{{ route('networks.edit', compact("user", "network")) }}">
                    <button class="btn btn-light"><i class="fas fa-pen fa-lg mr-2"></i>Edit</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('networks.destroy', compact("user", "network"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt fa-lg mr-2"></i>Delete</button>
                </form>
            </div>
        </div>

		{{-- Compilations Details --}}
		@include('compilations.show')

		<hr>
        <div class="row my-4">  {{-- LAYERS --}}
            <div class="col-12 align-self-center text-center h4">
                <span class="font-weight-bold"><i class="fas fa-layer-group fa-sm pr-2"></i>Layers number:</span>
                <span class="ml-3">{{$network->layers_number}}</span>
            </div>

            <div class="col-8 offset-2 mt-4" >
                {{-- RENDER MODEL LAYERS --}}
                @include('layers.show', compact('layers'))
            </div>
            
        </div>
    </div>
@endsection



