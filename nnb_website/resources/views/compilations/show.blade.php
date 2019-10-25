{{-- Compilations Details --}}
@if ($network->is_compiled)
    <div class="row my-3">
        <div class="col-12 align-self-center text-center h3">
            <span class="content-title">Compilation details</span>
        </div>
        <div class="col-12 text-muted d-block">Compile again to 'edit' the compilation details</div>
    </div>
    <div class="row my-2">
        <div class="col-6 align-self-center text-right font-weight-bold">Learning rate</div>
        <div class="col-6 align-self-center text-left">{{$compile->learning_rate}}</div>
    </div>
    <div class="row my-2">
        <div class="col-6 align-self-center text-right font-weight-bold">Optimizer</div>
        <div class="col-6 align-self-center text-left">{{$compile->optimizer}}</div>
    </div>
    <div class="row my-2">
        <div class="col-6 align-self-center text-right font-weight-bold">Metrics list:</div>
        <div class="col-6 align-self-center text-left">
            @if($compile->metrics)
                {{$compile->metrics}}
            @else
                <span class="font-italic">No metrics</span>
            @endif
        </div>
    </div>
    <div class="row my-2">
        <div class="col-6 align-self-center text-right font-weight-bold">Compiled at</div>
        <div class="col-6 align-self-center text-left">{{$compile->created_at}}</div>
    </div>
@endif