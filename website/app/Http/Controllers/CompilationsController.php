<?php

namespace App\Http\Controllers;

use App\User;
use App\Network;
use App\Compilation;
use App\Training;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompilationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user, Network $network)
    {
        if((Auth::user()->id != $user->id) && Auth::user()->rank != -1)
            return redirect(route("home"));

        $title = "Compile Model | Neural Network Builder";
        return view('compilations.create', compact("title", "user", "network"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user, Network $network)
    {
        if((Auth::user()->id != $user->id) && Auth::user()->rank != -1)
            return redirect(route("home"));
        
        // validate data
        $validateData = $request->validate([
            'learning_rate' => ['numeric', 'required', 'between:0.0001,10'],
            'optimizer' => ['required', 'max:50', 'string'],
            'metrics_list' => ['array', 'min:1'],
            'metrics_list.*' => ['string', 'in:accuracy'],
        ]);

        //Get compilations info
        $learning_rate = $request->learning_rate;
        $optimizer = $request->optimizer;
        $metrics_list = NULL;
        if(isset($request->metrics_list))
            $metrics_list = $request->metrics_list[0];  

        // if already exists a compilation for this model, then delete it
        $prev_compile = Compilation::where("model_id", $network->id);
        if($prev_compile)   $prev_compile->delete();

        $compile = Compilation::create([
            'model_id' => $network->id,
            'learning_rate' => $learning_rate,
            'optimizer' => $optimizer,
            //'metrics' => $metrics_list ? $metrics_list : NULL,
            'metrics' => $metrics_list,
            ]);

        try {
            $new_model_size = Compilation::compileModel($network, $optimizer, $learning_rate, $metrics_list);
            $network->is_compiled = True;
        } catch (\Throwable $th) {
            $compile->delete();
            return $th->getMessage();       //return to networks.index with error message "could not build the model: $th->getMessage()"
        }

        // Reset params
        $network->is_trained = false;
        $network->accuracy = NULL;
        $network->loss = NULL;
        $trainings = Training::where([
                ["model_id", $network->id],
                ["status", "<>", "error"]
            ])->get();
        foreach ($trainings as $train) {
            $train->status = "stopped";
            $train->return_message = "New Model compilation. Start now your training!";
            $train->training_percentage = 0;
            $train->update();
        }

        // Update user available_space
        $size_diff = $new_model_size - $network->file_size;
        $network->file_size = $new_model_size;
        if($user->available_space < $size_diff)
            $user->available_space = 0;
        else
            $user->available_space -= $size_diff;

        $user->update();
        $network->update();
        return redirect(route('networks.show', compact("user", "network")));
    }
}
