<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DatasetsController extends Controller
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

        $datasets = Dataset::where("user_id", $user->id);
        $title = "Datasets | Neural Network Builder";
        return view("datasets.index", compact("title", "user", "datasets"));
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

        $title = "Import Dataset | Neural Network Builder";
        return view('datasets.create', compact("title", "user"));
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

        $username = $request->username;
        $email = $request->email;

        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);    

        return redirect(route("datasets.index"));      //to change in ->  return redirect(route("datasets.show"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Dataset $dataset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Dataset $dataset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Dataset $dataset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Dataset $dataset)
    {
        //
    }
}
