import sys
import pickle
import json
import pandas as pd  # for csv files
import csv
import numpy as np
from keras.utils import to_categorical
from keras.models import load_model
from keras.callbacks import Callback
import time

# ----- Custom Callback ------

class MyCSVLogger(Callback):
    def __init__(self, filename):
        self.filename = filename
        self.file = None
        self.file_writer = None

    def on_test_begin(self, logs=None):
        try:
            # open csv file
            csv_file = open(self.filename, 'w')

            # init columns name
            columns_name = ['batch', 'size', 'accuracy', 'loss']
            writer = csv.DictWriter(csv_file, fieldnames=columns_name)
            writer.writeheader()

            # save file obj
            self.file = csv_file
            self.file_writer = writer
        except Exception as err:
            print("Error opening/writing the csv log file.")
            raise err

    def on_test_batch_begin(self, batch, logs=None):
        pass

    def on_test_batch_end(self, batch, logs=None):
        # sample content of logs {'batch': 0, 'size': 32, 'loss': 0.1504, 'accuracy': 0.9854}
        try:
            self.file_writer.writerow(logs)
        except Exception as err:
            print("Error writing csv log file.")
            raise err

    def on_test_end(self, logs=None):
        # close csv file
        self.file.close()

# ---------------------------------

def evaluateModel(model, x, y, batch=32, log_path=""):
    try:
        csv_logger = MyCSVLogger(log_path)
        final_values = model.evaluate(x, y, batch_size=batch, callbacks=[csv_logger])

        try:
            array_len = len(final_values)
        except Exception as err:
            array_len = 1

        if(array_len == 1): # no accuracy
            final_values = [final_values, ""]

        logs_dict = {
            "batch": "",
            "size": "",
            "accuracy": final_values[1],
            "loss": final_values[0],
        }

        with open(log_path, 'a') as f:
            for key in logs_dict.keys():
                if(key == "loss"):
                    separator = ""
                else:
                    separator = ","
                f.write("{}{}".format(logs_dict[key], separator))


    except Exception as err:
        print("Error evaluating the model.")
        raise err

# ---------------------------------

def loadLocalDataset(filename):
    try:
        if(filename.find(".pkl", -5) != -1 or filename.find(".pickle", -8) != -1):
            with open(filename, "rb") as inp:
                data = pickle.load(inp)
        elif(filename.find(".json", -6) != -1):
            with open(filename, "r") as inp:
                data = json.load(inp)
        elif(filename.find(".csv", -5) != -1):
            data = pd.read_csv(filename)
            #print("\nLocal Dataset", filename, "loaded!")
            test_x = data["test_x"].tolist()
            test_y = data["test_y"].tolist()
            return test_x, test_y

        #print("\nLocal Dataset", filename, "loaded!")
    except Exception as err:
        print("Error trying to Load data from", filename)
        raise err

    test_x = data["test_x"]
    test_y = data["test_y"]
    return test_x, test_y

# ---------------------------------

def evaluate():
    app_path = sys.argv[1]
    data_test_path = sys.argv[2]
    batch_size = int(sys.argv[3])
    model_path = sys.argv[4]
    output_classes = int(sys.argv[5])
    log_path = sys.argv[6]

    try:
        path_prefix = app_path + "/storage/app/"

        # Get Test Dataset
        test_x, test_y = loadLocalDataset(
            path_prefix + "public/" + data_test_path)

        if(output_classes > 1):
            test_y = to_categorical(test_y)

        # Load the model to train
        model = load_model(path_prefix + "public/" + model_path)

        # Start evaluation
        evaluateModel(model, test_x, test_y, batch_size, path_prefix + log_path)

    except Exception as err:
        print("\nCOULD NOT EVALUATE THE MODEL - ERROR: " + str(err))
        raise err

# ------ MAIN ------

evaluate()
