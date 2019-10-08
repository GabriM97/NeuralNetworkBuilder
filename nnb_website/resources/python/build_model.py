#!/usr/bin/python

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
                model.add(Dense(neurons_per_layer[0], activation=activ_functions[0], input_shape=data_shape))
            else:
                model.add(Dense(neurons_per_layer[layer], activation=activ_functions[layer]))

    elif(model_type == "func"):
        print("\nNOT SUPPORTED YET - WORK IN PROGRESS...")

    if get_info:
        print(model.summary())
    
    return model


def getLayersInfo(filepath):
    #filepath = /storage/app/users/$hashed_user/models/model_xx
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


def saveModel(model, filename):
    try:
        model.save(filename)
    except Exception as err:
        print("Error saving the model:", err)
        raise err


def importModel(modelPath):
    model = -1

    model = load_model(modelPath)
    if(model == -1):
        print("Error importing the model.")
    return model

#----------------------------------------------

def buildMethod():
    model_id = sys.argv[1]
    model_type = sys.argv[2]
    layers_number = int(sys.argv[3])
    local_dir = sys.argv[4]     # /storage/app/users/$hashed_user/models/
    data_shape = (int(sys.argv[5]),)
    get_info = True

    try:
        neurons_per_layer, activ_functions = getLayersInfo(local_dir + "model_" + model_id)
        model = buildModel(layers_number,
                            neurons_per_layer,
                            activ_functions,
                            data_shape,
                            model_type,
                            get_info)

        filename = local_dir + "model_" + model_id + ".h5"   # PHP SCRIPT
        #filename = "./saves/personal_model.h5"          # CMD SCRIPT
        saveModel(model, filename)

    except Exception as err:
        print(err)


# --- MAIN ---

buildMethod()
