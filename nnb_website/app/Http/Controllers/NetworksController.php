<?php

namespace App\Http\Controllers;

use App\Network;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Network $network)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Network $network)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Network  $network
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Network $network)
    {
        //
    }
}
