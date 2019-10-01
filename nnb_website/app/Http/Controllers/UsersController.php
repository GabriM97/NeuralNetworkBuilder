<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
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
    public function index()
    {
        // ADMIN ONLY
        
        $projects = Project::all();
        $title = "Our Projects";
        return view('projects.index', compact("title", "projects"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ADMIN ONLY

        $title = "Create Project";
        return view('projects.create', compact("title"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // ADMIN ONLY

        $project = new Project();
        $project->title = request('title');
        $project->description = request('description');
        $project->save();
        return redirect("/projects");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $title = "$user->username | NeuralNetworkBuilder";
        return view("users.show", compact("title", "user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('user.edit', ['user' => Auth::user()]));
        
        $title = "Edit profile | NeuralNetworkBuilder";
        return view('users.edit', compact("title", "user"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $process = $request->process;

        if((Auth::user()->id == $user->id) || (Auth::user()->rank == -1)){
            switch ($process) {
                case 'changeusername':  // CHANGE USERNAME (ADMIN ONLY)
                    if(Auth::user()->rank == -1){      
                        $new_username = $request->username;
                        $query = User::where("username", $new_username);
                        echo $query;
                        if($query){     // == NULL
                            $user->username = $new_username;
                            $status = 0;
                            $msg = "Username changed in <i>$new_username</i>";
                        }else{
                            $status = -1;
                            $msg = "Username <i>$new_username</i> already exists.";
                        }
                    }
                    break;
                case 'changeemail':     // CHANGE EMAIL
                    $current_psw = Hash::make($request->current_password);
                    if($current_psw !== $user->password){   //if password is wrong
                        $status = -1;
                        $msg = "Password wrong.";
                        break;
                    }

                    $new_email = $request->new_email;
                    $query = User::where("email", $new_email);
                    echo $query;
                    if($query){     // == NULL
                        $user->email = $new_email;
                        $status = 0;
                        $msg = "Email modified.";
                    }else{
                        $status = -1;
                        $msg = "Email <i>$new_email</i> already in use.";
                    }
                    break;
                case 'changepassword':      // CHANGE PASSWORD
                    $current_psw = Hash::make($request->current_password);
                    if($current_psw !== $user->password){   //if password is wrong
                        $status = -1;
                        $msg = "Password wrong.";
                        break;
                    }
                    $user->password = Hash::make($request->new_password);
                    $status = 0;
                    $msg = "Password modified.";
                    break;
                default:
                    $status = -1;
                    $msg = "Request not valid.";
                    break;
            }
            $user->update();    // $user->save();
        }else{
            $user = Auth::user();
            $status = -1;
            $msg = "Request not allowed.";
        }
        return redirect(route("user.show", 
                        ["user" => $user,
                         "return_status" => $status,
                         "return_msg" => $msg
                        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Auth::user()->rank == -1){
            $user->delete();
            return redirect(route("user.index"));
        }else{
            return redirect(route("home"));
        }
    }
}
