#!/usr/bin/python3

import sys
import pickle
import json
from keras.models import Sequential, load_model
from keras.layers import Dense


def buildModel(layers_number, neurons_per_layer, activ_functions, data_shape, model_type="Sequential", get_info=False):

    if(model_type != "Sequential" and model_type != "Functional"):
        print(model_type)
        print("\nSyntax model type ERROR! Set to default model type: Sequential")
        model_type = "Sequential"
    elif(model_type == "Functional"):
        print("\nFunctional model not supported yet! Set to default model type: Sequential")
        model_type = "Sequential"

    if(model_type == "Sequential"):
        model = Sequential()
        for layer in range(layers_number):
            if(layer == 0):
                model.add(Dense(
                    neurons_per_layer[0], activation=activ_functions[0], input_shape=data_shape))
            else:
                model.add(
                    Dense(neurons_per_layer[layer], activation=activ_functions[layer]))

    elif(model_type == "func"):
        print("\nNOT SUPPORTED YET - WORK IN PROGRESS...")

    if get_info:
        print(model.summary())

    return model


def getLayersInfo(filepath):
    # ../storage/app/users/$hashed_user/models/model_xx_layers_config.json
    filename = filepath + "_layers_config.json"

    try:
        with open(filename, "r") as inp:
            data = json.load(inp)

        neurons_per_layer = []
        for neur in data["neurons_number"]:
            neurons_per_layer.append(int(neur))
        activ_functions = data["activ_function"]

        return neurons_per_layer, activ_functions

    except IOError as err:
        print("\nError trying to load layers structure from", filename)
        raise err

# ----------------------------------------------

def buildMethod():
    model_id = sys.argv[1]
    model_type = sys.argv[2]
    layers_number = int(sys.argv[3])
    local_dir = sys.argv[4]     	# users/$hashed_user/models/
    data_shape = (int(sys.argv[5]),)
    get_info = False

    try:
        path_prefix = "../storage/app/"
        
        # ../storage/app/users/$hashed_user/models/model_xx
        neurons_per_layer, activ_functions = getLayersInfo(
            path_prefix + local_dir + "model_" + model_id)
            
        model = buildModel(layers_number,
                           neurons_per_layer,
                           activ_functions,
                           data_shape,
                           model_type,
                           get_info)

        # ../storage/app/public/users/$hashed_user/models/model_xx.h5
        filename = path_prefix + "public/" + local_dir + "model_" + model_id + ".h5"
        model.save(filename)

    except Exception as err:
        print("COULD NOT BUILD THE MODEL - ERROR: " + str(err))
        raise err

# --- MAIN ---

buildMethod()
