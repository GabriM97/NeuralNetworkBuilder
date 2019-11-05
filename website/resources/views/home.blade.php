@extends('layouts.app')

@section('page-title', $title)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="main-container card mt-5">
                    <div class="card-header text-white h4">Overview <i class="fas fa-search fa-rotate-90 ml-2"></i> </div>
                    <div class="card-body">

                        {{-- User --}}
                        <div class="row mb-2"> 
                            {{-- Username --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-user mr-2"></i>USER</h6>
                                        <p class="card-text mt-4 h4"> {{ $user->username }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- User rank --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-layer-group mr-2"></i>ACCOUNT TYPE</h6>
                                        @php
                                            switch ($user->rank){
                                                case -1:    // Admin
                                                    $color = "bg-danger";
                                                    break;
                                                case 0:    // Base user
                                                    $color = "bg-secondary";
                                                    break;
                                                case 1:    // Advanced user
                                                    $color = "bg-primary";
                                                    break;
                                                case 2:    // Professional user
                                                    $color = "bg-success text-dark";
                                                    break;
                                                default:
                                                    $color = "";
                                                    break;
                                            }
                                        @endphp
                                        <p class="card-text mt-4 h4">
                                            <span class="{{$color}} p-1">{{ ucfirst($user->getRank()) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Available space --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-database mr-2"></i>STORAGE</h6>
                                        <p class="card-text mt-4 h4">
                                            @php
                                                $size = $user->get_tot_files_size();
                                                if($size/1024 < 1000) 
                                                    echo round($size/1024, 2)." KB ";
                                                elseif($size/1048576 < 1000) 
                                                    echo round($size/1048576, 2)." MB ";
                                                else //if($size/1073741824 < 1000) 
                                                    echo round($size/1073741824, 2)." GB ";
                                            @endphp
                                            <span class="h4">
                                                of {{ (round($user->get_max_available_space()/1073741824, 2)." GB")}} used
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Training --}}
                        <div class="row my-2"> 
                            {{-- Tot Trainings --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-tools mr-2"></i>TOTAL TRAININGS</h6>
                                        <p class="card-text mt-4 h2"> {{ $trainings["num"] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Started Trainings --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-spinner fa-pulse mr-2"></i>TRAINING IN PROGRESS</h6>
                                        <p class="card-text mt-4 h2"> {{ $trainings["started"] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Paused Trainings --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-pause mr-2"></i>TRAINING IN PAUSE</h6>
                                        <p class="card-text mt-4 h2"> {{ $trainings["paused"] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Dataset --}}
                        <div class="row my-2"> 
                            {{-- Tot Datasets 
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">TOTAL DATASETS</h6>
                                        <p class="card-text mt-4 h2"> {{ $datasets["num"] }}</p>
                                    </div>
                                </div>
                            </div>--}}

                            {{-- Training Datasets --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-list-ul mr-2"></i>DATASETS FOR TRAINING</h5>
                                        <p class="card-text mt-4 h2"> {{ $datasets["train"] + $datasets["both"] }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Test Datasets --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-list-ul mr-2"></i>DATASETS FOR TEST</h5>
                                        <p class="card-text mt-4 h2"> {{ $datasets["test"] + $datasets["both"] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Size Datasets --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-archive mr-2"></i>TOT DATASETS SIZE</h6>
                                        <p class="card-text mt-4 h4">
                                            @php
                                                $size = $datasets["size"];
                                                if($size/1024 < 1000) 
                                                    echo round($size/1024, 2)." KB";
                                                elseif($size/1048576 < 1000) 
                                                    echo round($size/1048576, 2)." MB";
                                                else //if($size/1073741824 < 1000) 
                                                    echo round($size/1073741824, 2)." GB";
                                            @endphp
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Model --}}
                        <div class="row my-2"> 
                            {{-- Tot Models --}}
                            <div class="col-md">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-project-diagram mr-2"></i>TOTAL MODELS</h6>
                                        <p class="card-text mt-4 h2"> {{ $models["num"] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Trained Models --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-project-diagram mr-2"></i>TRAINED MODELS</h6>
                                        <p class="card-text mt-4 h2"> {{ $models["trained"] }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Size Models --}}
                            <div class="col-md">  
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><i class="fas fa-archive mr-2"></i>TOT MODELS SIZE</h6>
                                        <p class="card-text mt-4 h4">
                                            @php
                                                $size = $models["size"];
                                                if($size/1024 < 1000) 
                                                    echo round($size/1024, 2)." KB";
                                                elseif($size/1048576 < 1000) 
                                                    echo round($size/1048576, 2)." MB";
                                                else //if($size/1073741824 < 1000) 
                                                    echo round($size/1073741824, 2)." GB";
                                            @endphp
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection