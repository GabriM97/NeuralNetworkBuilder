{{-- ADMIN ONLY --}}

@if(Auth::user()->rank != -1)
    {{ redirect(route("home"))  }}
@endif

@extends("layouts.app")

@section('page-title', $title)

@section('content')

    <div class="container-fluid col-md-11 text-center">
        <a href="{{route("nodes.create")}}">
            <button class="btn btn-info">
                <i class="fas fa-plus fa-xs mr-0"></i>
                <i class="fas fa-network-wired fa-lg mr-2"></i>
                CREATE NEW NODE
            </button>
        </a>

        <h2 class="text-left mb-3 mt-3 text-left">Nodes</h2>
        
        <div class="main-container rounded p-1 my-2">
            <div class="row text-center font-weight-bold">    <!-- TITLE ROW -->
                <div class="col-md-1 align-self-center">Status</div>
                <div class="col-md-2 align-self-center">IP address</div>
                <div class="col-md-1 align-self-center">CPU cores</div>
                <div class="col-md-1 align-self-center">Total RAM</div>
                <div class="col-md-2 align-self-center">GPU model</div>
                <div class="col-md-1 align-self-center">Training in process</div>
                <div class="col-md-2 align-self-center">Added at</div>
                <div class="col-md-2 align-self-center">Action</div>
            </div>

            @foreach ($nodes as $node)
                <div class="row text-center text-break my-3">
                    <div class="col-md-1 align-self-center">
						@if ($node->status)
							<i class="text-success fas fa-circle mr-2"></i>ON
						@else
							<i class="text-danger fas fa-circle mr-2"></i>OFF
						@endif
					</div>
                    <div class="col-md-2 align-self-center">{{$node->ip_address}}</div>
                    <div class="col-md-1 align-self-center">
                        @if($node->cpu_numbers != NULL)
                            {{$node->cpu_numbers}}
                        @else

                        @endif
                    </div>
					<div class="col-md-1 align-self-center px-0">{{round($node->total_ram/1073741824, 2)}} GB</div>
					<div class="col-md-2 align-self-center pr-2 pl-2">{{$node->gpu_details}}</div>
                    <div class="col-md-1 align-self-center">{{$node->running_trainings}}</div>
                    <div class="col-md-2 align-self-center">{{$node->created_at}}</div>
                    <div class="col-md-2 align-self-center">
                        {{-- DETAILS BUTTON --}}         
                        <a href="{{ route('nodes.show', ['node' => $node]) }}" class="text-decoration-none" title="Details">
                            <button class="btn btn-primary btn-circle"><i class="fas fa-list-ul"></i></button>
                        </a>

                        {{-- EDIT BUTTON --}}         
						<a href="{{ route('nodes.edit', ['node' => $node]) }}" class="text-decoration-none" title="Edit">
							<button class="btn btn-light btn-circle"><i class="fas fa-pen"></i></button>
                        </a>
                        
                        {{-- DELETE BUTTON --}}
                        <form class="form-delete d-inline" method="POST" action="{{route('nodes.destroy', ['node' => $node])}}">
                            @csrf
                            @method("DELETE")
                            <button class="btn btn-danger btn-circle" type="submit" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
