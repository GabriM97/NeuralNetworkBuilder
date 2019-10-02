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
        if(Auth::user()->rank !== -1)
            return redirect(route("home"));
        
        $users = User::all();
        $title = "All users | Neural Network Builder";
        return view('users.index', compact("title", "users"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ADMIN ONLY
        if(Auth::user()->rank !== -1)
            return redirect(route("home"));

        $title = "Create new User | Neural Network Builder";
        return view('users.create', compact("title"));
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
        if(Auth::user()->rank !== -1)
            return redirect(route("home"));


        $username = $request->username;
        $email = $request->email;
        $query_username = User::where("username", $username)->get();
        $query_email = User::where("email", $email)->get();

        $status = -1;
        if(count($query_username) != 0){
            $msg = "The username has already been taken.";
            return view("users.create", compact("status", "msg"));
        }

        if(count($query_email) != 0){
            $msg = "The email has already been taken.";
            return view("users.create", compact("status", "msg"));
        }

        // DA CONTINUARE

        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);    

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
                        $query = User::where("username", $new_username)->get();
                        if(count($query) == 0){
                            $user->username = $new_username;
                            $status = 0;
                            $msg = "Username changed in $new_username";
                        }else{
                            $status = -1;
                            $msg = "Username $new_username already exists.";
                        }
                    }
                    break;

                case 'changeemail':     // CHANGE EMAIL
                    if(!(Hash::check($request->current_password, $user->password))){   //if password is wrong
                        $status = -1;
                        $msg = "Password wrong.";
                        break;
                    }
                    $new_email = $request->new_email;
                    $query = User::where("email", $new_email)->get();
                    if(count($query) == 0){
                        $user->email = $new_email;
                        $status = 0;
                        $msg = "Email modified.";
                    }else{
                        $status = -1;
                        $msg = "Email $new_email already in use.";
                    }
                    break;

                case 'changepassword':      // CHANGE PASSWORD
                    if(!(Hash::check($request->current_password, $user->password))){   //if password is wrong
                        $status = -1;
                        $msg = "Current password wrong.";
                        break;
                    }
                    $user->password = Hash::make($request->new_password);
                    $status = 0;
                    $msg = "Password modified.";
                    break;
                    
                case 'upgradeaccount':
                    if(Auth::user()->rank == -1){
                        if($user->rank == 0 ){    //if Base
                            $user->rank += 1;
                            $user->available_space += 8589934592;    // +8 GB   (10GB MAX)
                        }else if($user->rank == 1){  //if Advanced
                            $user->rank += 1;
                            $user->available_space += 21474836480;    // +20 GB   (30GB MAX)
                        }else{
                            $status = -1;
                            $msg = "Request not valid.";
                            break;
                        }
                        $status = 0;
                        $msg = "User upgraded.";
                    }
                    break;

                case 'downgradeaccount':
                    if(Auth::user()->rank == -1){
                        if($user->rank == 1 ){    //if Advanced
                            $user->rank -= 1;
                            $user->available_space -= 8589934592;    // -8 GB   (2GB MAX)
                        }else if($user->rank == 2){  //if Professional
                            $user->rank -= 1;
                            $user->available_space -= 21474836480;    // -20 GB   (10GB MAX)
                        }else{
                            $status = -1;
                            $msg = "Request not valid.";
                            break;
                        }
                        $status = 0;
                        $msg = "User downgraded.";
                    }
                    break;

                case 'makeadmin':
                    if(Auth::user()->rank == -1){
                        if($user->rank != -1){
                            $user->rank = -1;
                            $user->available_space = 1073741824000;     // +1000 GB  
                            $status = 0;
                            $msg = "User is now Admin.";
                        }else{
                            $status = -1;
                            $msg = "Request not valid.";
                        }
                    }
                    break;
                
                case 'removeadmin':
                    if(Auth::user()->rank == -1){
                        if($user->rank == -1){
                            $user->rank = 0;
                            $user->available_space -= 1071594340352;     // -998 GB  (2 GB of Base Account)
                            $status = 0;
                            $msg = "User is no more Admin.";
                        }else{
                            $status = -1;
                            $msg = "Request not valid.";
                        }
                    }
                    break;

                default:
                    $status = -1;
                    $msg = "Request not valid.";
                    break;
            }
            $user->update();
        }else{
            $user = Auth::user();
            $status = -1;
            $msg = "Request not allowed.";
        }
        $return_status = $status;
        $return_msg = $msg;
        $title = "$user->username | NeuralNetworkBuilder";
        return view("users.show", compact("user", "title", "return_status", "return_msg"));
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
            //$user->delete();      //DO NOTHING ATM
            return redirect(route("user.index"));
        }else{
            return redirect(route("home"));
        }
    }
}
