<?php

namespace App\Http\Controllers;

use App\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NodesController extends Controller
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

        $nodes = Node::all();
        $title = "Nodes | Neural Network Builder";
        return view("nodes.index", compact("title", "nodes"));
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

        $title = "Add new Node | Neural Network Builder";
        return view('nodes.create', compact("title"));
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
            'ip_address' => ['unique:nodes', 'required', 'ipv4'],
            'description' => ['max:255', 'string', 'nullable'],
        ]);

        $ip_addr = $request->ip_address;
        $description = $request->description;

        $node = Node::create([
            'ip_address' => $ip_addr,
            'description' => $description,
        ]);

        $hw_info = $node->getNodeHwInfo();
        if($hw_info['status']){ // if node is ON
            $node->running_trainings = 0;
            $node->status = $hw_info['status'];
            $node->cpu_description = $hw_info['cpu']["model"];
            $node->cpu_numbers = $hw_info['cpu']["threads"];
            $node->gpu_details = $hw_info['gpu']["model"];
            $node->total_ram = $hw_info['ram']["total"];
            $node->save();
        }

        $title = "$node->ip_address Node | NeuralNetworkBuilder";
        return view("nodes.show", compact("node", "title"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function show(Node $node)
    {
        if(Auth::user()->rank !== -1)
            return redirect(route('home'));
            
        $title = "$node->ip_address Node | NeuralNetworkBuilder";
        return view("nodes.show", compact("title", "node"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function edit(Node $node)
    {
        if(Auth::user()->rank !== -1)
            return redirect(route('home'));
        
        $title = "Edit Node | NeuralNetworkBuilder";
        return view('nodes.edit', compact("title", "node"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Node $node)
    {
        if(Auth::user()->rank !== -1)
            return redirect(route('home'));
        
        $validateData = $request->validate([
            'ip_address' => ['required', 'ipv4'],
            'description' => ['max:255', 'string', 'nullable'],
        ]);

        if($node->ip_address !== $request->ip_address){
            $request->validate(['ip_address' => ['unique:nodes']]);
        }

        //Get node info
        $node->ip_address = $request->ip_address;
        $node->description = $request->description;
        $node->update();

        $hw_info = $node->getNodeHwInfo();
        if($hw_info['status']){ // if node is ON
            $node->status = $hw_info['status'];
            $node->cpu_description = $hw_info['cpu']["model"];
            $node->cpu_numbers = $hw_info['cpu']["threads"];
            $node->gpu_details = $hw_info['gpu']["model"];
            $node->total_ram = $hw_info['ram']["total"];
        }else{
            $node->cpu_description = NULL;
            $node->cpu_numbers = NULL;
            $node->gpu_details = NULL;
            $node->total_ram = NULL;
        }
        $node->update();
        return redirect(route("nodes.show", compact("node")));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function destroy(Node $node)
    {
        if(Auth::user()->rank != -1)
            return redirect(route("home"));
        
        $node->delete();
        return redirect(route("nodes.index"));
    }

    /**
     * Refresh Hardware Info of the specified resource.
     *
     * @param  \App\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function refreshHWInfo(Node $node)
    {
        if(Auth::user()->rank != -1)
            return redirect(route("home"));
        
        $hw_info = $node->getNodeHwInfo();
        if($hw_info['status']){ // if node is ON
            $node->status = $hw_info['status'];
            $node->cpu_description = $hw_info['cpu']["model"];
            $node->cpu_numbers = $hw_info['cpu']["threads"];
            $node->gpu_details = $hw_info['gpu']["model"];
            $node->total_ram = $hw_info['ram']["total"];
        }
        $node->update();
        return redirect(route("nodes.show", compact("node")));
    }
}



