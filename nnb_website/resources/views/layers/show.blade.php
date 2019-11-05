<div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
    <div class="col-md-3 py-1 align-self-center"><i class="fas fa-layer-group pr-1"></i>Layer</div>
    <div class="col-md-3 py-1 align-self-center">Layer type</div>
    <div class="col-md-3 py-1 align-self-center"><i class="fas fa-code-branch pr-1"></i>Neurons</div>
    <div class="col-md-3 py-1 align-self-center">Activation function</div>
    {{-- <div class="col-md-3 align-self-center">Action</div> --}}
</div>

@foreach ($layers as $lyr)
    <div class="row border border-secondary text-center">
        <div class="col-md-3 py-1 align-self-center">{{$loop->iteration}}</div>
        <div class="col-md-3 py-1 align-self-center">{{$lyr->layer_type}}</div>
        <div class="col-md-3 py-1 align-self-center">{{$lyr->neurons_number}}</div>
        <div class="col-md-3 py-1 align-self-center">{{$lyr->activation_function}}</div>
        {{-- <div class="col-md-3 align-self-center">
            {{-- DELETE BUTTON --}/}
            <form class="form-delete d-inline-block" method="POST" action="{{route('layers.destroy', ['network' => $model, 'layer' => $lyr])}}">
                @csrf
                @method("DELETE")
                <button class="btn btn-danger" type="submit">Delete</button>
            </form>
        </div> --}}
    </div>
@endforeach