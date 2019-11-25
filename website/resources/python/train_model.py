import sys
import pickle
import json
import pandas as pd  # for csv files
import numpy as np
from keras.utils import to_categorical
from keras.models import load_model
from keras.callbacks import CSVLogger
from keras.callbacks import ModelCheckpoint


def trainModel(model, x, y, num_epochs, batch_dim=32, verb=0, valid_split=0.0, log_path="", save_best=0, checkpoint_path=""):
    #print("\n***** Start training *****\n")

    # Callbacks
    callbacks_list = list()
    callbacks_list.append(CSVLogger(log_path))
    callbacks_list.append(ModelCheckpoint(filepath=checkpoint_path, save_best_only=save_best))

    try:
        model.fit(x, y,
                  epochs=num_epochs,
                  batch_size=batch_dim,
                  verbose=verb,
                  validation_split=valid_split,
                  callbacks=callbacks_list)

        #print("\n***** Training completed *****")
        return model

    except Exception as err:
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
            train_x = data["train_x"].tolist()
            train_y = data["train_y"].tolist()
            return train_x, train_y

        #print("\nLocal Dataset", filename, "loaded!")
    except Exception as err:
        print("\nError trying to Load data from", filename)
        raise err

    train_x = data["train_x"]
    train_y = data["train_y"]
    return train_x, train_y


def train():
    app_path = sys.argv[1]
    data_train_path = sys.argv[2]
    model_path = sys.argv[3]
    epochs = int(sys.argv[4])
    batch_size = int(sys.argv[5])
    valid_split = float(sys.argv[6])
    output_classes = int(sys.argv[7])
    checkpoint_path = sys.argv[8]
    save_best_model = int(sys.argv[9])
    epochs_log = sys.argv[10]
    verbose = 2

    try:
        path_prefix = app_path + "/storage/app/"

        # Get Training Dataset
        train_x, train_y = loadLocalDataset(path_prefix + "public/" + data_train_path)

        if(output_classes > 1):
            train_y = to_categorical(train_y)

        # Load the model to train
        model = load_model(path_prefix + "public/" + model_path)

        epochs_log = path_prefix + epochs_log               # app/storage/path/to/log.txt
        checkpoint_path = path_prefix + checkpoint_path     # app/storage/path/to/checkpoint/model_id.h5
        # Start training
        model = trainModel(model, train_x, train_y, epochs,
                           batch_size, verbose, valid_split, 
                           epochs_log, save_best_model, checkpoint_path)

        # Save trained model
        model.save(path_prefix + "public/" + model_path)

    except Exception as err:
        print("\nCOULD NOT TRAIN THE MODEL - ERROR: " + str(err))
        raise err

# --- MAIN ---


train()
