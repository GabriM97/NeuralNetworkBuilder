@extends("layouts.app")

@section('page-title', $title)

@section('content')

<div class="container text-center">
    <a href="{{route("datasets.create", ['user' => $user])}}">
        <button class="btn btn-info"><strong>+</strong> Import new Dataset</button>
    </a>
</div>

<h2 class="ml-5">Your datasets</h2>
    <div class="container text-center">
        <div class="row border border-secondary text-center font-weight-bold">    <!-- TITLE ROW -->
            <div class="col-sm">ID</div>
            <div class="col-sm">Title</div>
            <div class="col-sm">Filesize</div>
            <div class="col-sm">Input shape</div>
            <div class="col-sm">Output classes</div>
            <div class="col-sm">Dataset type</div>
            <div class="col-sm">Last time used</div>
            <div class="col-sm">Action</div>
            <div class="col-sm">Action</div>
        </div>

        @foreach ($datasets as $data)
            <div class="row border border-secondary text-center">
                <div class="col-sm">{{$data->id}}</div>
                <div class="col-sm">{{$data->data_name}}</div>
                <div class="col-sm">    {{-- FILE SIZE --}}
                    @if ($data->file_size/1048576 >= 1024)
                        {{ $data->file_size/1073741824 }} GB
                    @else
                        {{ $data->file_size/1048576 }} MB 
                    @endif
                </div>
                <div class="col-sm">{{$data->x_shape}}</div>
                <div class="col-sm">{{$data->y_classes}}</div>
                <div class="col-sm">    {{-- DATA TYPE --}}
                    @php
                        if($data->is_train) echo "Train";
                        if($data->is_test) echo "Test";
                        if($data->is_generic) echo "Generic";
                    @endphp
                </div>
                <div class="col-sm">{{$data->last_time_used}}</div>
                <div class="col-sm">    {{-- DETAILS BUTTON --}}
                    <a href="{{ route('datasets.show', ['user' => $user, 'dataset' => $data]) }}">
                        <button class="btn btn-primary">Details</button>
                    </a>
                </div>
                <div class="col-sm">    {{-- DELETE BUTTON --}}
                    <a href="{{ route('datasets.destroy', ['user' => $user, 'dataset' => $data]) }}">
                        <button class="btn btn-primary">Details</button>
                    </a>
                </div>
          </div>
        @endforeach
    </div>
@endsection
