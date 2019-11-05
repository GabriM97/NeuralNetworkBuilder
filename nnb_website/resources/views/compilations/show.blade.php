{{-- Compilations Details --}}
@if ($network->is_compiled)
    <div class="align-self-center text-center my-3">
        <div class="d-inline-block my-0 align-self-center h3">
            <span class="content-title"><i class="fas fa-barcode pr-2"></i>Compilation details</span>
        </div>

        {{-- COMPILE BUTTON --}}
        <div class="d-inline-block align-self-center">
            {{-- COMPILE BUTTON --}}
            <a href="{{ route("compilations.create", compact('user', 'network')) }}">
                <button class="btn btn-sm btn-success text-dark">Compile<i class="fas fa-barcode fa-lg pl-2"></i></button>
            </a>
        </div>

        <div class="w-100"></div>

        <div class="col text-muted d-block">Compile again to edit the compilation details</div>
    </div>
    <div class="row my-2 px-5">
        <div class="col-md align-self-center text-md-right font-weight-bold">Learning rate</div>
        <div class="col-md align-self-center text-md-left">{{$compile->learning_rate}}</div>
    </div>
    <div class="row my-2 px-5">
        <div class="col-md align-self-center text-md-right font-weight-bold">Optimizer</div>
        <div class="col-md align-self-center text-md-left">{{$compile->optimizer}}</div>
    </div>
    <div class="row my-2 px-5">
        <div class="col-md align-self-center text-md-right font-weight-bold">Metrics list:</div>
        <div class="col-md align-self-center text-md-left">
            @if($compile->metrics)
                {{$compile->metrics}}
            @else
                <span class="font-italic">No metrics</span>
            @endif
        </div>
    </div>
    <div class="row my-2 px-5">
        <div class="col-md-6 align-self-center text-md-right font-weight-bold">Compiled at</div>
        <div class="col-md-6 align-self-center text-md-left">{{$compile->created_at}}</div>
    </div>
@endif