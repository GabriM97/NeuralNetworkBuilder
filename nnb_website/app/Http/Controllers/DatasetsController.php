<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


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

        $datasets = Dataset::where("user_id", $user->id)->get();
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

        // validate data
        $validateData = $request->validate([
            'dataset_file' => ['file', 'required'],
            'title' => ['required', 'max:50', 'string'],
            'description' => ['max:255', 'string', 'nullable'],
            'input_shape' => ['numeric', 'between:1,1000', 'required'],
            'output_classes' => ['numeric', 'between:1,1000', 'required'],
            'dataset_type' => ['in:train,test,generic', 'required', 'string'],
        ]);

        // File data
        $dataset_file = $request->file("dataset_file");
        $file_size = $dataset_file->getClientSize();
        $file_extension = $dataset_file->getClientOriginalExtension();
        $hashed_user = hash("md5", $user->id);  
        $local_dir = "users/$hashed_user/datasets/";
        
        //Check user available space
        if($user->available_space < $file_size)
            return redirect(route("datasets.index", ["user" => $user]));

        // Get Dataset info
        $user_id = $user->id;
        $title = $request->title;
        $description = $request->description;
        $input_shape = $request->input_shape;
        $output_classes = $request->output_classes;
        $dataset_type = $request->dataset_type;

        // Set Data type
        $isTrain = false;
        $isTest = false;
        $isGeneric = false;
        if($dataset_type == "train")    $isTrain = true;
        if($dataset_type == "test")     $isTest = true;
        if($dataset_type == "generic")  $isGeneric = true;

        // Add dataset record
        $dataset = Dataset::create([
            'user_id' => $user_id,
            'data_name' => $title,
            'data_description' => $description,
            'file_size' => $file_size,
            'file_extension' => $file_extension,
            'x_shape' => $input_shape,
            'y_classes' => $output_classes,
            'local_path' => $local_dir,     //save path only
            'is_train' => $isTrain,
            'is_test' => $isTest,
            'is_generic' => $isGeneric,
        ]);

        try {
            // Store dataset
            $id = $dataset->id;
            $filename = "data_$id.$file_extension";
            $local_path = $local_dir.$filename;
            $dataset_file->storeAs("public/$local_dir", $filename);
            Storage::setVisibility("public/$local_path", 'public');
            $dataset->local_path = $local_path;     //save path+filename
            $dataset->save();

            //Update user details
            $user->datasets_number++;
            $user->available_space -= $file_size;
            $user->save();
        } catch (\Throwable $th) {
            $dataset->delete();
        }
        
        return route("datasets.show", ["user" => $user, "dataset" => $dataset]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Dataset $dataset)
    {   
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('datasets.index', ['user' => Auth::user()]));
            
        $title = "$dataset->data_name | NeuralNetworkBuilder";
        return view("datasets.show", compact("title", "user", "dataset"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Dataset $dataset)
    {
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('datasets.index', ['user' => Auth::user()]));
        
        $title = "Edit dataset | NeuralNetworkBuilder";
        return view('datasets.edit', compact("title", "user", "dataset"));
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
        if((Auth::user()->id !== $user->id) && (Auth::user()->rank !== -1))
            return redirect(route('datasets.index', ['user' => Auth::user()]));

        $dataset->data_name = $request->title;
        $dataset->data_description = $request->description;
        $dataset->x_shape = $request->x_input;
        $dataset->y_classes = $request->y_output;

        // Set Data type
        $dataset_type = $request->dataset_type;
        $isTrain = false;
        $isTest = false;
        $isGeneric = false;
        if($dataset_type == "train")    $isTrain = true;
        if($dataset_type == "test")     $isTest = true;
        if($dataset_type == "generic")  $isGeneric = true;
        $dataset->is_train = $isTrain;
        $dataset->is_test = $isTest;
        $dataset->is_generic = $isGeneric;

        $dataset->update();
        return redirect(route("datasets.show", compact("user", "dataset")));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Dataset $dataset)
    {
        if(Auth::user()->id == $user->id || Auth::user()->rank == -1){
            Storage::delete("public/$dataset->local_path");
            $dataset->delete();
            $user->datasets_number--;

            $tot_size = $user->get_tot_files_size();
            if($user->available_space > 0)  $user->available_space += $dataset->file_size;
            else    // avb_spc = 0
                if($tot_size < $user->get_max_available_space()) 
                    $user->available_space = $user->get_max_available_space() - $tot_size;
                else
                    $user->available_space = 0;

            $user->save();
            return redirect(route("datasets.index", compact("user")));
        }else{
            return redirect(route("home"));
        }
    }

    public function download(User $user, Dataset $dataset)
    {
        if(Auth::user()->id == $user->id || Auth::user()->rank == -1){
           return Storage::disk('public')->download($dataset->local_path, "dataset_$dataset->data_name.$dataset->file_extension");
        }else
            return redirect(route("home"));
    }
}
