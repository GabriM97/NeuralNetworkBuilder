<?php

namespace App\Http\Controllers;

use App\User;
use App\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

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
        $title = "Users | Neural Network Builder";
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

        $validateData = $request->validate([
            'username' => ['unique:users', 'required', 'max:255', 'string'],
            'email' => ['email', 'unique:users', 'required', 'max:255','string'],
            'password' => ['min:8', 'required', 'string'],
        ]);

        $username = $request->username;
        $email = $request->email;

        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);

        $hashed_user = hash("md5", $user->id); 
        $user_dir_datasets = "users/$hashed_user/datasets";
        $user_dir_models = "users/$hashed_user/models";
        $user_dir_trainings = "users/$hashed_user/trainings";
        
        // make private directories
        Storage::makeDirectory($user_dir_datasets);
        Storage::makeDirectory($user_dir_models);
        Storage::makeDirectory($user_dir_trainings);

        // make public directories
        Storage::makeDirectory("public/$user_dir_datasets");
        Storage::makeDirectory("public/$user_dir_models");
        Storage::makeDirectory("public/$user_dir_trainings");
        
        $user->save();

        $return_status = 0;
        $return_msg = "User $user->username created!";
        $title = "$user->username | NeuralNetworkBuilder";
        return view("users.show", compact("user", "return_status", "return_msg", "title"));
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
            return redirect(route('users.edit', ['user' => Auth::user()]));
        
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
        if((Auth::user()->id == $user->id) || (Auth::user()->rank == -1)){
            $process = $request->process;

            $validateData = $request->validate([
                'process' => ['in:changeusername,changeemail,changepassword,upgradeaccount,downgradeaccount,makeadmin,removeadmin', 'required', 'string']
            ]);

            switch ($process) {
                case 'changeusername':  // CHANGE USERNAME (ADMIN ONLY)
                    if(Auth::user()->rank == -1){   
                        $validateData = $request->validate(['username' => ['required', 'max:255', 'string']]);
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
                    $validateData = $request->validate([
                        'new_email' => ['email', 'required', 'max:255','string'],
                        'confirm_new_email' => ['email', 'required', 'max:255','string'],
                    ]);
                    
                    if(Auth::user()->rank != -1){
                        $validateData = $request->validate(['current_password' => ['min:8', 'required', 'string'],]);
                        if(!(Hash::check($request->current_password, $user->password))){   //if password is wrong
                            $status = -1;
                            $msg = "Password wrong.";
                            break;
                        }
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
                    $validateData = $request->validate([
                        'new_password' => ['min:8', 'required', 'string'],
                        'confirm_new_password' => ['min:8', 'required', 'string'],
                    ]);

                    if(Auth::user()->rank != -1){
                        $validateData = $request->validate(['current_password' => ['min:8', 'required', 'string'],]);
                        if(!(Hash::check($request->current_password, $user->password))){   //if password is wrong
                            $status = -1;
                            $msg = "Current password wrong.";
                            break;
                        }
                    }
                    $user->password = Hash::make($request->new_password);
                    $status = 0;
                    $msg = "Password modified.";
                    break;
                    
                case 'upgradeaccount':
                    if(Auth::user()->rank == -1){
                        if($user->rank != 0  && $user->rank != 1){ 
                            $status = -1;
                            $msg = "Request not valid.";
                            break;
                        }
                        $user->rank += 1;
                        $tot_size = $user->get_tot_files_size();
                        if($tot_size > $user->get_max_available_space())   //available_space < 0
                            $user->available_space = 0;
                        else
                            $user->available_space = $user->get_max_available_space() - $tot_size;
                            
                        $status = 0;
                        $msg = "User upgraded.";
                    }
                    break;

                case 'downgradeaccount':
                    if(Auth::user()->rank == -1){
                        if($user->rank != 1  && $user->rank != 2){ 
                            $status = -1;
                            $msg = "Request not valid.";
                            break;
                        }
                        $user->rank -= 1;
                        $tot_size = $user->get_tot_files_size();
                        if($tot_size > $user->get_max_available_space())   //available_space < 0
                            $user->available_space = 0;
                        else
                            $user->available_space = $user->get_max_available_space() - $tot_size;
                            
                        $status = 0;
                        $msg = "User downgraded.";
                    }
                    break;

                case 'makeadmin':
                    if(Auth::user()->rank == -1){
                        if($user->rank != -1){
                            $user->rank = -1;
                            $user->available_space = $user->get_max_available_space();     // 1000 GB  
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

                            $tot_size = $user->get_tot_files_size();
                            $available = $user->get_max_available_space() - $tot_size;
                            $user->available_space = $available < 0 ? 0 : $available;
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
            $user->delete();            
            Storage::deleteDirectory("public/users/".hash("md5", $user->id));
            Storage::deleteDirectory("users/".hash("md5", $user->id));
            return redirect(route("users.index"));
        }else{
            return redirect(route("home"));
        }
    }
}
