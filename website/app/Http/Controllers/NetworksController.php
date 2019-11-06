<?php

namespace App\Http\Controllers;

use App\Network;
use App\Layer;
use App\User;
use App\Compilation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class NetworksController extends Controller
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

        $networks = Network::where("user_id", $user->id)->get();
        $title = "Models | Neural Network Builder";
        return view("networks.index", compact("title", "user", "networks"));
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

        $title = "Build Model | Neural Network Builder";
        return view('networks.create', compact("title", "user"));
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
            'model_type' => ['string', 'required', 'in:Sequential,Functional'],
            'title' => ['required', 'max:50', 'string'],
            'description' => ['max:255', 'string', 'nullable'],
            'input_shape' => ['numeric', 'between:1,1000', 'required'],
            'output_classes' => ['numeric', 'between:1,1000', 'required'],
            'layers_number' => ['numeric', 'between:1,100', 'required'],
            'neurons_number' => ['array', 'min:1', 'required'],
            'neurons_number.*' => ['numeric', 'between:1,500', 'required'],
            'activ_funct' => ['array', 'min:1', 'required'],
            'activ_funct.*' => ['string', 'required', 'in:relu,sigmoid,tanh,linear,softmax'],
        ]);

        //Get model info
        $user_id = $user->id;
        $model_type = $request->model_type;
        $title = $request->title;
        $description = $request->description;
        $input_shape = $request->input_shape;
        $output_classes = $request->output_classes;
        $layers_num = $request->layers_number;
        $neurons_number = $request->neurons_number;
        $activ_function = $request->activ_funct;

        $hashed_user = hash("md5", $user_id);  
        $local_dir = "users/$hashed_user/models/";
        
        //check output_classes = last_layer_neurons
        if($output_classes != $neurons_number[$layers_num-1])
            return redirect(route("networks.create", compact("user")));
        
        // Add network record
        $network = Network::create([
            'user_id' => $user_id,
            'model_type' => $model_type,
            'input_shape' => $input_shape,
            'layers_number' => $layers_num,
            'output_classes' => $output_classes,
            'model_name' => $title,
            'model_description' => $description,
            'file_size' => 0,
            'local_path' => $local_dir,
            ]);

        // Add layers records
        LayersController::create(
            $network->id, 
            $layers_num, 
            $neurons_number, 
            $activ_function
        );
        
        try {
            // Build the model
            $model_id = $network->id;
            $model_size = Network::build_h5_model($model_id, $local_dir, $model_type, $layers_num, $input_shape, $neurons_number, $activ_function);
        
        } catch (\Throwable $th) {
            $network->delete();
            //$layers->delete();
            return $th->getMessage();       //return to networks.index with error message "could not build the model: $th->getMessage()"
        }

        //Check user available space and set model file_size
        if($user->available_space < $model_size){
            Storage::delete("public/$network->local_path");
            $network->delete();
            return redirect(route("networks.index", ["user" => $user]));       // redirect with error message "no space available"
        }

        $network->local_path = $local_dir."model_$model_id.h5";     //save path+filename
        $network->file_size = $model_size;
        $network->save();
        
        //Update user details
        $user->models_number++;
        $user->available_space -= $model_size;
        $user->save();

        return redirect(route("compilations.create", compact("user", "network")));
        //return redirect(route("networks.show", ["user" => $user, "network" => $network->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Network $network)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('networks.index', ['user' => Auth::user()]));
        
        $layers = LayersController::getModelLayers($network->id);
        $compile = Compilation::where('model_id', $network->id)->get()->first();
        $title = "$network->model_name | NeuralNetworkBuilder";
        return view("networks.show", compact("title", "user", "network", "layers", isset($compile) ? "compile" : NULL));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Network $network)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('networks.index', ['user' => Auth::user()]));
        
        $layers = LayersController::getModelLayers($network->id);
        $title = "Edit model | NeuralNetworkBuilder";
        return view('networks.edit', compact("title", "user", "network", "layers"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Network $network)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('networks.index', ['user' => Auth::user()]));

        // validate data
        $validateData = $request->validate([
            'title' => ['required', 'max:50', 'string'],
            'description' => ['max:255', 'string', 'nullable'],
            //'input_shape' => ['numeric', 'between:1,1000', 'required'],
            //'output_classes' => ['numeric', 'between:1,1000', 'required'],
            //'layers_number' => ['numeric', 'between:1,100', 'required'],
            //'neurons_number' => ['array', 'min:1', 'required'],
            //'neurons_number.*' => ['numeric', 'between:1,500', 'required'],
            //'activ_funct' => ['array', 'min:1', 'required'],
            //'activ_funct.*' => ['string', 'required', 'in:relu,sigmoid,tanh,linear,softmax'],
        ]);

        //Get model info
        $network->model_name = $request->title;
        $network->model_description = $request->description;
        //$network->input_shape = $request->input_shape;
        //$network->output_classes = $request->output_classes;
        //$network->layers_number = $request->layers_number;
        $network->update();

        //neurons_number = $request->neurons_number;
        //$activation_function = $request->activ_funct;
        //LayersController::edit();
        
        return redirect(route("networks.show", compact("user", "network")));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Network $network)
    {
        if(Auth::user()->id == $user->id || Auth::user()->rank == -1){
            $model_folder = substr($network->local_path, 0, strrpos($network->local_path, "/"));
            Storage::delete("public/$network->local_path");
            Storage::delete("$model_folder/model_$network->id"."_layers_config.json");
            
            $network->delete();
            $user->models_number--;

            $tot_size = $user->get_tot_files_size();
            if($user->available_space > 0)  $user->available_space += $network->file_size;
            else    // avb_spc = 0
                if($tot_size < $user->get_max_available_space()) 
                    $user->available_space = $user->get_max_available_space() - $tot_size;
                else
                    $user->available_space = 0;

            $user->save();
            return redirect(route("networks.index", ["user" => $user])); 
        }else{
            return redirect(route("home"));
        }
    }

    public function download(User $user, Network $network)
    {
        if(Auth::user()->id == $user->id || Auth::user()->rank == -1){
           return Storage::disk('public')->download($network->local_path, "model_$network->model_name.h5");
        }else
            return redirect(route("home"));
    }
}
