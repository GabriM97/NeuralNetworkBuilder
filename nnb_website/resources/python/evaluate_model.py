import sys
import pickle
import json
import pandas as pd   #for csv files
import numpy as np
from keras.utils import to_categorical
from keras.models import load_model

def evaluateModel(model, x, y, metrics_list):

    try:
        loss = model.evaluate(x, y)

        print("\nLoss:", round(loss[0], 4))
        i=1
        for metric in metrics_list:
            print(metric + ":", loss[i])
            i+=1
        return 0
    except Exception as e:
        print("Error", e, "evaluating the model.")
        return -1

def importModel():
    filename = "./python/saves/personal_model.h5"     # PHP SCRIPT
    #filename = "./saves/personal_model.h5"            # CMD SRIPT
    model = -1
    model = load_model(filename)
    if(model == -1):
        print("Error importing the model.")

    return model

def loadLocalDataset(filename):
    path = "./python/saves/" + filename     # PHP SCRIPT
    #path = "./saves/" + filename            # CMD SRIPT

    try:
        if(filename.find(".pkl", -5) != -1):
            with open(path, "rb") as inp:
                data = pickle.load(inp)
        elif(filename.find(".json", -6) != -1):
            with open(path, "r") as inp:
                data = json.load(inp)
        elif(filename.find(".csv", -5) != -1):
            data = pd.read_csv(path)
            #print("\nLocal Dataset", filename, "loaded!")
            test_x = data["test_x"].tolist()
            test_y = data["test_y"].tolist()
            return test_x, test_y

        #print("\nLocal Dataset", filename, "loaded!")
    except Exception as e:
        print("\n", e, "Error trying to Load data from", filename)
        return -1, -1

    test_x = data["test_x"]
    test_y = data["test_y"]
    return test_x, test_y

# ---------------------------------

def evaluate():
    filename = sys.argv[1]
    test_x, test_y = loadLocalDataset(filename)
    model = importModel()
    output_classes = int(sys.argv[2])
    #metrics_list = sys.argv[3]
    metrics_list = ["accuracy"]

    if(output_classes > 2):
        test_y = to_categorical(test_y)

    exit = evaluateModel(model, test_x, test_y, metrics_list)
    print("exit_status:", exit)

# --- MAIN ---

evaluate()
