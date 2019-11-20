{{-- ADMIN ONLY --}}

@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="container mb-5">
        <div class="row">
            <div class="col h5">
                <a class="text-decoration-none rounded text-white p-2" href="{{route("nodes.index")}}">
                    <i class="fas fa-arrow-circle-left mr-2"></i>Nodes
                </a>
            </div>
        </div>
    </div>

    {{--     
    @if(isset($return_status))
        @php
            if($return_status == 0) $msg_class = "alert-success";
            else $msg_class = "alert-danger";
        @endphp
    
        <div class="container text-center alert {{$msg_class}}" role="alert">{{$return_msg}}</div>
    @endif
    --}}
    
    <div class="main-container rounded container col-md-6 p-2 my-5">
        <div class="align-self-center text-center my-3">
            <div class="d-inline-block align-self-center text-right">
                <h2 class="content-title m-0"><i class="fas fa-network-wired fa-xs mr-2"></i>Node details</h2>
            </div>

            {{-- REFRESH DETAILS BUTTON --}}
            <div class="d-inline-block align-self-center text-left">
                <a href="{{ route("nodes.refresh", compact('node')) }}">
                    <button class="btn btn-sm btn-warning">Refresh details<i class="fas fa-sync-alt ml-2"></i></button>
                </a>
            </div>
        </div>
        
        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Status</div>
            <div class="col-md-6 text-break align-self-center">
                @if ($node->status)
                    <i class="text-success fas fa-circle mr-2"></i>ON
                @else
                    <i class="text-danger fas fa-circle mr-2"></i>OFF
                @endif
            </div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">IP Address</div>
            <div class="col-md-6 text-break align-self-center">{{ $node->ip_address }}</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 align-self-center text-md-right font-weight-bold">Description</div>
            <div class="col-md-6 align-self-center text-md-left">
                @if($node->description)     {{-- description != NULL --}}
                    {{$node->description}}
                @else
                    <span class="font-italic">No description</span>
                @endif
            </div>
        </div>
        
        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">CPU Model</div>
            <div class="col-md-6 align-self-center">{{ $node->cpu_description }}</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right text-break align-self-center font-weight-bold">CPU Threads</div>
            <div class="col-md-6 align-self-center">{{ $node->cpu_numbers }}</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">GPU Details</div>
            <div class="col-md-6 align-self-center">{{ $node->gpu_details }}</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Total available RAM</div>
            <div class="col-md-6 align-self-center">{{ round($node->total_ram/1073741824, 2) }} GB</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Trainings in process</div>
            <div class="col-md-6 align-self-center">{{ $node->running_trainings }}</div>
        </div>

        <div class="row my-2 px-5">
            <div class="col-md-6 text-md-right align-self-center font-weight-bold">Added at</div>
            <div class="col-md-6 align-self-center">{{ $node->created_at }}</div>
        </div>

        {{-- Buttons --}}
        <div class="row mt-5">
            <div class="col-6 text-right">   {{-- EDIT BUTTON --}}         
                <a href="{{ route('nodes.edit', compact("node")) }}">
                    <button class="btn btn-light"><i class="fas fa-pen fa-lg mr-2"></i>Edit</button>
                </a>
            </div>
            <div class="col-6 text-left">   {{-- DELETE BUTTON --}}
                <form class="form-delete d-inline-block" method="POST" action="{{route('nodes.destroy', compact("node"))}}">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt fa-lg mr-2"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection



