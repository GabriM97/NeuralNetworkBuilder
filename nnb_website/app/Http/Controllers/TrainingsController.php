<?php

namespace App\Http\Controllers;

use App\Training;
use App\Network;
use App\Dataset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class TrainingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        if((Auth::user()->id != $user->id) && (Auth::user()->rank !== -1))
            return redirect(route("home"));

        $trainings = Training::where("user_id", $user->id)->get();
        $title = "Trainings | Neural Network Builder";
        return view("trainings.index", compact("title", "user", "trainings"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        if((Auth::user()->id != $user->id))
            return redirect(route("home"));

        $datasets = Dataset::where("user_id", $user->id)->get();
        $models = Network::where([
                ["user_id", "=", $user->id],
                ["is_compiled", "=", "1"],
            ])->get();

        $title = "New training | Neural Network Builder";
        return view('trainings.create', compact("title", "user", "models", "datasets"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        if((Auth::user()->id != $user->id))
            return redirect(route("home"));

        // validate data
        $validateData = $request->validate([
            'model_id' => ['numeric', 'required', 'gt:0', 
                Rule::exists('networks','id')->where(function($query) use ($user){
                    $query->where("user_id", $user->id)
                        ->where("is_compiled", 1);
                    })],

            'training_dataset' => ['numeric', 'required', 'gt:0', 
                Rule::exists('datasets','id')->where(function($query) use ($user){
                    $query->where([
                                ["user_id", $user->id],
                                ["is_train", 1]
                            ])
                            ->orWhere([
                                ["user_id", $user->id],
                                ["is_generic", 1]
                            ]);
                    })],
                
            'test_dataset' => ['numeric', 'gt:0', 'nullable',
                Rule::exists('datasets','id')->where(function($query) use ($user){
                    $query->where([
                                ["user_id", $user->id],
                                ["is_test", 1]
                            ])
                            ->orWhere([
                                ["user_id", $user->id],
                                ["is_generic", 1]
                            ]);
                    })],

            'epochs' => ['numeric', 'between:1,10000', 'required'],
            'batch_size' => ['numeric', 'between:1,10000', 'required'],
            'validation_split' => ['numeric', 'between:0,0.99', 'required'],
            'save_best' => ['numeric', 'in:0,1', 'required'],
        ]);
        
        // Get training info
        $user_id = $user->id;
        $model_id = $request->model_id;
        $train_data_id = $request->training_dataset;
        $test_data_id = $request->test_dataset;
        $epochs = $request->epochs;
        $batch_size = $request->batch_size;
        $valid_split = $request->validation_split;
        $save_best = $request->save_best;       

        $training = Training::create([
            'user_id' => $user_id,
            'model_id' => $model_id,
            'dataset_id_training' => $train_data_id,
            'dataset_id_test' => $test_data_id ? $test_data_id : NULL,
            'is_evaluated' => $test_data_id ? True : False,
            'epochs' => $epochs,
            'batch_size' => $batch_size,
            'validation_split' => $valid_split,
            'save_best_only' => $save_best,
            'checkpoint_filepath' => NULL,
            'filepath_epochs_log' => NULL,
            ]);
        
        // make path
        $hashed_user = hash("md5", $user_id);  
        $training_path = "users/$hashed_user/trainings/$training->id";
        Storage::makeDirectory("$training_path/checkpoints");

        // store paths
        $training->checkpoint_filepath = "$training_path/checkpoints/";
        $training->filepath_epochs_log = "$training_path/epochs_log.txt";
        $training->update();

        $model = Network::find($model_id);
        $dataset_training = Dataset::find($train_data_id);
        $dataset_test = Dataset::find($test_data_id);

        $model->last_time_used = Carbon::now();
        $dataset_training->last_time_used = Carbon::now();
        $dataset_test->last_time_used = Carbon::now();
        
        $model->update();
        $dataset_training->update();
        $dataset_test->update();

        return redirect(route("trainings.show", compact("user", "training")));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Training $training)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('trainings.index', ['user' => Auth::user()]));

        $network = Network::find($training->model_id);
        $dataset_train = Dataset::find($training->dataset_id_training);
        $dataset_test = Dataset::find($training->dataset_id_test);

        return view("trainings.show", compact("user", "training", 'network', 'dataset_train', $dataset_test ? 'dataset_test' : NULL));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function edit(Training $training)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Training $training)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(Training $training)
    {
        //
    }

    public function start(User $user, Training $training)
    {

        
        return $training;
    }
}
