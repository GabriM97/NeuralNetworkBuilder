<?php

namespace App\Http\Controllers;

use App\Training;
use App\Network;
use App\Dataset;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $title = $user->username." Dashboard | NeuralNetworkBuilder";
        
        $datasets = array(
            "num" => Dataset::where("user_id", $user->id)->count(),
            "train" => Dataset::where([["user_id", $user->id],["is_train", 1]])->count(),
            "test" => Dataset::where([["user_id", $user->id],["is_test", 1]])->count(),
            "both" => Dataset::where([["user_id", $user->id],["is_generic", 1]])->count(),
            "size" => Dataset::where("user_id", $user->id)->sum("file_size"),
        );

        $models = array(
            "num" => Network::where("user_id", $user->id)->count(),
            "trained" => Network::where([["user_id", $user->id],["is_trained", 1]])->count(),
            "size" => Network::where("user_id", $user->id)->sum("file_size"),
        );

        $trainings = array(
            "num" => $user->getTrainingNumbers(),
            "started" => Training::where([["user_id", $user->id],["status", "started"]])->count(),
            "paused" => Training::where([["user_id", $user->id],["status", "paused"]])->count(),
        );

        return view('home', compact("title", "user", "datasets", "models", "trainings"));
    }
}
