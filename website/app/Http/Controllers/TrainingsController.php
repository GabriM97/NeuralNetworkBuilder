<?php

namespace App\Http\Controllers;

use App\Training;
use App\Network;
use App\Dataset;
use App\User;
use App\Jobs\TrainingJob;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
//use Illuminate\Support\Facades\Bus;

use Carbon\Carbon;
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

            'description' => ['max:255', 'string', 'nullable'],
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
        $description = $request->description;
        $epochs = $request->epochs;
        $batch_size = $request->batch_size;
        $valid_split = $request->validation_split;
        $save_best = $request->save_best;       

        $training = Training::create([
            'user_id' => $user_id,
            'model_id' => $model_id,
            'dataset_id_training' => $train_data_id,
            'dataset_id_test' => $test_data_id ? $test_data_id : NULL,
            'train_description' => $description,
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
        $training->filepath_epochs_log = "$training_path/epochs_log.csv";
        $file_log = fopen(storage_path()."/app/".$training->filepath_epochs_log, "w");
        fclose($file_log);
        $training->update();

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
    public function edit(User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $training->status == 'started')
            return redirect(route('trainings.index', ['user' => Auth::user()]));
    
        $datasets = Dataset::where("user_id", $user->id)->get();
        $models = Network::where([
                ["user_id", "=", $user->id],
                ["is_compiled", "=", "1"],
            ])->get();

        $title = "Edit training | NeuralNetworkBuilder";
        return view('trainings.edit', compact("title", "user", "training", "datasets", "models"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $training->status == 'started')
            return redirect(route('trainings.index', ['user' => Auth::user()]));

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

            'description' => ['max:255', 'string', 'nullable'],
            'epochs' => ['numeric', 'between:1,10000', 'required'],
            'batch_size' => ['numeric', 'between:1,10000', 'required'],
            'validation_split' => ['numeric', 'between:0,0.99', 'required'],
            'save_best' => ['numeric', 'in:0,1', 'required'],
        ]);

        // Get new training info
        $training->train_description = $request->description;

        if($training->status != 'paused'){
            if($training->model_id != $request->model_id){
                $training->training_percentage = 0;
                if($training->status == "error"){
                    $training->status = "stopped";
                    $training->return_message = "Try to training with this new settings.";
                }
            }
            $training->model_id = $request->model_id;
        }

        if($training->dataset_id_training != $request->training_dataset){
            if($training->status == "error"){
                $training->status = "stopped";
                $training->return_message = "Try to training with this new settings.";
            }
            $training->dataset_id_training = $request->training_dataset;
        }

        if(!$request->test_dataset)     $training->is_evaluated = false;
        else    $training->is_evaluated = true;
        if($training->dataset_id_test != $request->test_dataset){
            if($training->status == "error"){
                $training->status = "stopped";
                $training->return_message = "Try to training with this new settings.";
            }
            $training->dataset_id_test = $request->test_dataset;
        }

        if($training->status != "paused"){
            $training->epochs = $request->epochs;
            $training->batch_size = $request->batch_size;
            $training->validation_split = $request->validation_split;
        }
        $training->save_best_only = $request->save_best;

        $training->update();
        return redirect(route("trainings.show", compact("user", "training")));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Training $training)
    {
        if(Auth::user()->id == $user->id || Auth::user()->rank == -1){
            $training_folder = substr($training->filepath_epochs_log, 0, strrpos($training->filepath_epochs_log, "/"));
            Storage::deleteDirectory($training_folder);

            $training->delete();
            return redirect(route("trainings.index", ["user" => $user])); 
        }else{
            return redirect(route("home"));
        }
    }

    /**
     * Send training data to fetch() js request.
     *
     * @param  Illuminate\Http\Request $request, \App\User $user, \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function getTrainingInfo(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $request->_type != 'update_data')
            return redirect(route('trainings.index', ['user' => Auth::user()]));

        // Get training model
        $model = Network::find($training->model_id);

        // build response with updated values
        $response = array(
            "return_message" => $training->return_message,
            "in_queue" => $training->in_queue,
            "status" => $training->status,
            "train_perc" => $training->training_percentage,
            "evaluation_in_progress" => $training->evaluation_in_progress,
            "accuracy" => $model->accuracy,
            "loss" => $model->loss,
            "epoch" => $training->executed_epochs
        );

        return $response;
    }

    /**
     * Start the specified training.
     *
     * @param  \App\User $user, \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $training->status == 'started' || $training->status == 'error' || $request->_type != 'start')
            return redirect(route('trainings.show', compact("user", "training")));

        // $user = from_parameter
        $network = Network::find($training->model_id);
        $dataset_train = Dataset::find($training->dataset_id_training);
        $dataset_test = Dataset::find($training->dataset_id_test);

        $trainingJob = (new TrainingJob($training, $user, $network, $dataset_train, $training->is_evaluated ? $dataset_test : NULL));
        dispatch($trainingJob)
            ->onQueue($user->getRank());

        $training->in_queue = true;
        $training->return_message = "Training is in queue. It will be scheduled as soon as possible based on your Account Type.";   //add "... Account Type ($user->rank)." with it's type-name
        $training->training_percentage = 0;     // not necessary
        if(!$network->is_trained){
            $network->is_trained = true;
            $network->accuracy = 0;
            $network->loss = 0;
        }
        $training->update();
        $network->update();

        return redirect(route("trainings.show", compact("user", "training")));
    }
    
    /**
     * Pause the specified training.
     *
     * @param  \App\User $user, \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function pause(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $training->status != 'started' || $request->_type != 'pause' || $training->evaluation_in_progress)
            return redirect(route('trainings.show', compact("user", "training")));

        try {
            $training_pid = $training->process_pid;
            if(!posix_kill($training_pid, SIGTERM))
                throw new Exception("Error sending Pause signal.");

        } catch (Exception $err) {
            $training->return_message = $err->getMessage()." Could not pause the training.";
            $training->update();
            //throw $err;
        }

        // move the model from user checkpoint path to user public dir
        $model = Network::find($training->model_id);
        Storage::move("public/".$model->local_path, "public/".$model->local_path.".backup");

        try {
            Storage::move($training->checkpoint_filepath."model_$training->model_id.h5", "public/".$model->local_path);
            Storage::delete("public/".$model->local_path.".backup");
        } catch (\Throwable $th) {
            Storage::move("public/".$model->local_path.".backup", "public/".$model->local_path);
        }

        // Update user->available_space with new model_size
        $model_size_after = Storage::size("public/".$model->local_path);

        $size_diff = $model_size_after - $model->file_size;
        $model->file_size = $model_size_after;

        if($user->available_space < $size_diff)
            $user->available_space = 0;
        else 
            $user->available_space -= $size_diff;
        
        $model->update();
        $user->update();

        return redirect(route("trainings.show", compact("user", "training")));
    }

    /**
     * Stop the specified training.
     *
     * @param  \App\User $user, \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function stop(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $request->_type != 'stop' || ($training->status != 'started' && $training->status != 'paused'))
            return redirect(route('trainings.show', compact("user", "training")));
            
        if($training->status == 'started'){
            $training->executed_epochs = 0;
            try {
                $training_pid = $training->process_pid;
                if(!posix_kill($training_pid, SIGKILL))
                    throw new Exception("Error sending Pause signal.");

            } catch (Exception $err) {
                $training->status = "error";
                $training->return_message = $err->getMessage()." Could not stop the training.";
                $training->update();
            }
        }else
            if($training->status == 'paused'){
                $training->status = "stopped";
                $training->return_message = "Training stopped sucesfully.";
                $training->executed_epochs = 0;
                $training->update();
            }

        // move the model from user checkpoint path to user public dir
        $model = Network::find($training->model_id);
        Storage::move("public/".$model->local_path, "public/".$model->local_path.".backup");

        try {
            Storage::move($training->checkpoint_filepath."model_$training->model_id.h5", "public/".$model->local_path);
            Storage::delete("public/".$model->local_path.".backup");
        } catch (\Throwable $th) {
            Storage::move("public/".$model->local_path.".backup", "public/".$model->local_path);
        }

        // Update user->available_space with new model_size
        $model_size_after = Storage::size("public/".$model->local_path);

        $size_diff = $model_size_after - $model->file_size;
        $model->file_size = $model_size_after;

        if($user->available_space < $size_diff)
            $user->available_space = 0;
        else 
            $user->available_space -= $size_diff;
        
        $model->update();
        $user->update();

        return redirect(route("trainings.show", compact("user", "training")));
    }

    /**
     * Resume the specified training.
     *
     * @param  \App\User $user, \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function resume(Request $request, User $user, Training $training)
    {
        if(((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1)) || $training->status != 'paused' || $request->_type != 'resume' || $training->evaluation_in_progress)
            return redirect(route('trainings.show', compact("user", "training")));

        // $user = from_parameter
        $network = Network::find($training->model_id);
        $dataset_train = Dataset::find($training->dataset_id_training);
        $dataset_test = Dataset::find($training->dataset_id_test);

        $trainingJob = (new TrainingJob($training, $user, $network, $dataset_train, $training->is_evaluated ? $dataset_test : NULL));
        dispatch($trainingJob)
            ->onQueue($user->getRank());

        $training->in_queue = true;
        $training->return_message = "Training is in queue. It will be scheduled as soon as possible based on your Account Type.";   //add "... Account Type ($user->rank)." with it's type-name
        $training->update();

        return redirect(route("trainings.show", compact("user", "training")));
    }
}