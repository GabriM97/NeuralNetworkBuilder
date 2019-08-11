import sys
import pickle
import json
from keras.models import Sequential, load_model
from keras.layers import Dense

def buildModel(layers_number, neurons_per_layer, activ_functions, data_shape, model_type="Seq", get_info=False):

    model_type = model_type.lower()
    if(model_type != "seq" and model_type != "func"):
        print("\nSyntax model type ERROR! Set to default model type: Sequential")
        model_type = "seq"
    elif(model_type == "func"):
        print("\nFunctional model not supported yet! Set to default model type: Sequential")
        model_type = "seq"

    if(model_type == "seq"):
        model = Sequential()
        for layer in range(layers_number):
            if(layer == 0):
                model.add(Dense(neurons_per_layer[0], activation=activ_functions[0], input_shape=data_shape))
            else:
                model.add(Dense(neurons_per_layer[layer], activation=activ_functions[layer]))

    elif(model_type == "func"):
        print("\nNOT SUPPORTED YET - WORK IN PROGRESS...")

    print(model.summary()) if get_info else print("\nModel Builded!")
    return model


def getLayersInfo(filename):
    try:
        with open(filename, "r") as inp:
            data = json.load(inp)
    except IOError:
        print("\nError trying to load layers structure from", filename)
        return -1

    neurons_per_layer = data["neurons"]
    activ_functions = data["activ_function"]

    return neurons_per_layer, activ_function


def saveModel(model, filename):
    try:
        model.save(filename)
        return 0
    except Exception as e:
        print("Error saving the model:", e)
        return -1

#----------------------------------------------

def method(layers_number, neurons_per_layer, activ_functions, data_shape, model_type, get_info):

    model = buildModel(layers_number,
                       neurons_per_layer,
                       activ_functions,
                       data_shape,
                       model_type,
                       get_info)

    exit = saveModel(model, "personal_model.h5")
    print("exit_status:", exit)

# --- MAIN ---

model_type = sys.argv[1]
layers_number = sys.argv[2]
neurons_per_layer, activ_functions = getLayersInfo(sys.argv[3])
data_shape = sys.argv[4]
get_info = False

method(layers_number, neurons_per_layer, activ_functions, data_shape, model_type, get_info)
