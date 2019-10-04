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

    <div class="container col-7 text-sm-center">
        <h2 class="content-title mb-5">Dataset details</h2>
        <div class="row">
            <!-- Left column -->
            <div class="col-7">
                <div class="row">
                    <div class="col-4 text-right font-weight-bold">Title</div>
                    <div class="col-8 text-left">{{ $dataset->data_name }}</div>
                </div>
                <div class="row">
                    <div class="col-4 text-right font-weight-bold">Description</div>
                    <div class="col-8 border text-left">
                        @if($dataset->data_description)
                            {{$dataset->data_description}}
                        @else
                            <span class="font-italic">No description</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 text-right font-weight-bold">Data type</div>
                    <div class="col-6 text-left"> {{-- DATA TYPE --}}
                        @php
                            if($dataset->is_train) echo "Train";
                            if($dataset->is_test) echo "Test";
                            if($dataset->is_generic) echo "Generic";
                        @endphp
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 text-right font-weight-bold">Last time used</div>
                    <div class="col-8 text-left">
                        @if($dataset->last_time_used)
                                {{$dataset->last_time_used}}
                        @else
                            <span class="font-italic">Never</span>
                        @endif    
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 text-right font-weight-bold">Uploaded at</div>
                    <div class="col-8 text-left">{{$dataset->created_at}}</div>
                </div>
            </div>

            <!-- Right column -->
            <div class="col-5">
                
                @if(Auth::user()->rank == -1 && $user->id != Auth::user()->id)
                    <div class="row">
                        <div class="col-7 text-right font-weight-bold">Owner</div>
                        <div class="col-5 text-left">{{$user->username}}</div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-7 text-right font-weight-bold">File size</div>
                    <div class="col-5 text-left pr-0">    {{-- FILE SIZE --}}
                        @php
                            if($dataset->file_size/1024 < 1000) 
                                echo round($dataset->file_size/1024, 2)." KB";
                            elseif($dataset->file_size/1048576 < 1000) 
                                echo round($dataset->file_size/1048576, 2)." MB";
                            else //if($dataset->file_size/1073741824 < 1000) 
                                echo round($dataset->file_size/1073741824, 2)." GB";
                        @endphp
                    </div>
                </div>
                <div class="row">
                    <div class="col-7 text-right font-weight-bold">File extension</div>
                    <div class="col-5 text-left">{{$dataset->file_extension}}</div>
                </div>
                <div class="row">
                    <div class="col-7 text-right font-weight-bold">Input shape</div>
                    <div class="col-5 text-left">{{$dataset->x_shape}}</div>
                </div>
                <div class="row">
                    <div class="col-7 text-right font-weight-bold">Output classes</div>
                    <div class="col-5 text-left">{{$dataset->y_classes}}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 text-right">   {{-- EDIT BUTTON --}}         
                <a href="{{ route('datasets.edit', compact("user", "dataset")) }}">
                    <button class="btn btn-primary">Edit</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('datasets.destroy', compact("user", "dataset"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection



