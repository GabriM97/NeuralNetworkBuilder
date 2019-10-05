<?php

namespace App\Http\Controllers;

use App\Network;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NetworksController extends Controller
{
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        //
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
