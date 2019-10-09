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

    <div class="container col-8 text-sm-center">
        <div class="row my-5">
            <div class="col offset-1 align-self-center text-right">
                <h2 class="content-title m-0">Model details</h2>
            </div>

            {{-- DOWNLOAD BUTTON --}}
            <div class="col align-self-center text-left">
                <a href="{{ route("networks.download", compact('user', 'network')) }}">
                    <button class="btn btn-sm btn-outline-dark">Download</button>
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Left column -->
            <div class="col-3 offset-2">
                <div class="row my-2">
                    <div class="col-7 align-self-center text-right font-weight-bold">Model type</div>
                    <div class="col-5 align-self-center text-left">{{$network->model_type}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-7 align-self-center text-right font-weight-bold">Layers number</div>
                    <div class="col-5 align-self-center text-left">{{$network->layers_number}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-7 align-self-center text-right font-weight-bold">Input shape</div>
                    <div class="col-5 align-self-center text-left">{{$network->input_shape}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-7 align-self-center text-right font-weight-bold">Output classes</div>
                    <div class="col-5 align-self-center text-left">{{$network->output_classes}}</div>
                </div>
                <div class="row my-2">
                    <div class="col-7 align-self-center text-right font-weight-bold">File size</div>
                    <div class="col-5 align-self-center text-left pr-0">    {{-- FILE SIZE --}}
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
            </div>

            <!-- Right column -->
            <div class="col-6 offset-1 text-break">

                @if(Auth::user()->rank == -1 && $user->id != Auth::user()->id)
                    <div class="row my-2">
                        <div class="col-4 align-self-center text-right font-weight-bold">Owner</div>
                        <div class="col-8 align-self-center text-left">{{$user->username}}</div>
                    </div>
                @endif

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
                    <div class="col-4 align-self-center text-right font-weight-bold">Last time used</div>
                    <div class="col-8 align-self-center text-left">
                        @if($network->last_time_used)
                                {{$network->last_time_used}}
                        @else
                            <span class="font-italic">Never</span>
                        @endif    
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-4 align-self-center text-right font-weight-bold">Uploaded at</div>
                    <div class="col-8 align-self-center text-left">{{$network->created_at}}</div>
                </div>
            </div>
        </div>

        <div class="row"></div>

        <div class="row mt-5">
            <div class="col-6 text-right">   {{-- EDIT BUTTON --}}         
                <a href="{{ route('networks.edit', compact("user", "network")) }}">
                    <button class="btn btn-primary disabled" disabled>Edit</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('networks.destroy', compact("user", "network"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection



