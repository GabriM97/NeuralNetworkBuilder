<?php

namespace App\Http\Controllers;

use App\Training;
use App\Network;
use App\Dataset;
use App\User;
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
                    $query->where("user_id", $user->id)
                        ->where("is_train", 1)
                        ->orWhere("is_generic", 1);
                    })],
                
            'test_dataset' => ['numeric', 'required', 'gt:0', 'nullable',
                Rule::exists('datasets','id')->where(function($query) use ($user){
                    $query->where("user_id", $user->id)
                        ->where("is_test", 1)
                        ->orWhere("is_generic", 1);
                    })],

            'epochs' => ['numeric', 'between:1,10000', 'required'],
            'batch_size' => ['numeric', 'between:1,10000', 'required'],
            'validation_split' => ['numeric', 'between:0,0.99', 'required'],
        ]);

        return $validateData;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        //
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
}
