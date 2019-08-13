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

    print(model.summary()) if get_info else print("\nModel builded!")
    return model


def getLayersInfo(filename):
    try:
        with open(filename, "r") as inp:
            data = json.load(inp)
    except IOError:
        print("\nError trying to load layers structure from", filename)
        return -1

    neurons_per_layer = []
    for neur in data["neurons_number"]:
        neurons_per_layer.append(int(neur))
    activ_functions = data["activ_function"]

    return neurons_per_layer, activ_functions


def saveModel(model, filename):
    try:
        model.save(filename)
        return 0
    except Exception as e:
        print("Error saving the model:", e)
        return -1


def importModel(modelPath):
    model = -1

    model = load_model(modelPath)
    if(model == -1):
        print("Error importing the model.")

    return model


#----------------------------------------------

def buildMethod():
    model_type = sys.argv[1]
    layers_number = int(sys.argv[2])
    neurons_per_layer, activ_functions = getLayersInfo(sys.argv[3])
    data_shape = (int(sys.argv[4]),)
    get_info = True

    model = buildModel(layers_number,
                       neurons_per_layer,
                       activ_functions,
                       data_shape,
                       model_type,
                       get_info)

    filename = "./python/saves/personal_model.h5"   # PHP SCRIPT
    #filename = "./saves/personal_model.h5"          # CMD SCRIPT
    exit = saveModel(model, filename)
    print("exit_status:", exit)


def importMethod():
    filename = "./python/saves/personal_model.h5"   # PHP SCRIPT
    #filename = "./saves/personal_model.h5"          # CMD SCRIPT
    model = importModel(filename)

    if(model != -1):
        print("exit_status: 0")
    else:
        print("exit_status:", model)


# --- MAIN ---

buildMethod()
#importMethod()
