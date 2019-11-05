<?php

namespace App\Http\Controllers;

use App\Layer;
use Illuminate\Http\Request;

class LayersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create(int $model_id, int $layers_number, array $neurons_number, array $activ_function)
    {
        $layer_type = "dense";
        $is_output = false;

        for ($i=0; $i < $layers_number; $i++) {
            if($i == $layers_number-1) $is_output = true;
            Layer::create([
                'model_id' => $model_id,
                'layer_type' => $layer_type,
                'neurons_number' => $neurons_number[$i],
                'activation_function' => $activ_function[$i],
                'is_output' => $is_output
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Layer  $layer
     * @return \Illuminate\Http\Response
     */
    public function show(Layer $layer)
    {
        //
    }

    public static function getModelLayers(int $model_id){

        return Layer::where("model_id", $model_id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Layer  $layer
     * @return \Illuminate\Http\Response
     */
    public function edit(Layer $layer)
    {
        $layers = Layer::where("model_id", $network->id)->get();
        foreach($layers as $layer){
            $layer->neurons_number = $request->neurons_number;
            $layer->activation_function = $request->activ_funct;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Layer  $layer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layer $layer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Layer  $layer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layer $layer)
    {
        //
    }
}
