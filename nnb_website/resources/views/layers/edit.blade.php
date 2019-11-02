<div class="col">
    <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
        <div class="col-md-3 py-1 align-self-center">Layer</div>
        <div class="col-md-3 py-1 align-self-center">Layer type</div>
        <div class="col-md-3 py-1 align-self-center">Neurons</div>
        <div class="col-md-3 py-1 align-self-center">Activation function</div>
        {{-- <div class="col-md-3 align-self-center">Action</div> --}}
    </div>
    
    @foreach ($layers as $lyr)
        <div class="row border border-secondary text-center">
            <div class="col-md-3 py-1 align-self-center">L{{$loop->iteration}}</div>
            <div class="col-md-3 py-1 align-self-center">{{$lyr->layer_type}}</div>
            <div class="col-md-3 py-1 align-self-center">
                <input class="form-control @error('input_shape') is-invalid @enderror" type="number" name="neurons_number[]" value="{{$lyr->neurons_number}}" min="1" max="500" step="1">
            </div>
            <div class="col-md-3 py-1 align-self-center">
                <select class="form-control" name="activ_funct[]">   
                    <option value="relu" @if($lyr->activation_function == 'relu') selected @endif>ReLU</option>
                    <option value="sigmoid" @if($lyr->activation_function == 'sigmoid') selected @endif>Sigmoid</option>
                    <option value="tanh" @if($lyr->activation_function == 'tanh') selected @endif>Tanh</option>
                    <option value="linear" @if($lyr->activation_function == 'linear') selected @endif>Linear</option>
                    <option value="softmax" @if($lyr->activation_function == 'softmax') selected @endif>Softmax</option>
                </select>
            </div>
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
</div>