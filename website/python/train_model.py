import sys
import pickle
import json
from keras.utils import to_categorical
from keras.models import load_model

def trainModel(model, x, y, num_epochs, batch_dim=32, verb=0, valid_split=0.0):
    print("\nStart training.")
    model.fit(x, y,
              epochs=num_epochs,
              batch_size=batch_dim,
              verbose=verb,
              validation_split=valid_split)

    print("\nTraining completed.")
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
        #print("\nLocal Dataset", filename, "loaded!")
    except IOError:
        print("\nError trying to Load data from", filename)
        return -1

    train_x = data["train_x"]
    train_y = data["train_y"]

    return train_x, train_y

#---------------------------------
def saveModel(model):
    filename = "./python/saves/personal_model.h5"     # PHP SCRIPT
    #filename = "./saves/personal_model.h5"            # CMD SRIPT
    try:
        model.save(filename)
        return 0
    except Exception as e:
        print("Error saving the model:", e)
        return -1


def importModel():
    filename = "./python/saves/personal_model.h5"     # PHP SCRIPT
    #filename = "./saves/personal_model.h5"            # CMD SRIPT
    model = -1
    model = load_model(filename)
    if(model == -1):
        print("Error importing the model.")

    return model

def train():
    filename = sys.argv[1]
    train_x, train_y = loadLocalDataset(filename)
    model = importModel()
    epochs = int(sys.argv[2])
    batch_size = int(sys.argv[3])
    valid_split = float(sys.argv[4])
    output_classes = int(sys.argv[5])
    verbose = 2

    if(output_classes > 2):
        train_y = to_categorical(train_y)

    model = trainModel(model, train_x, train_y, epochs, batch_size, verbose, valid_split)
    exit = saveModel(model)
    print("exit_status:", exit)

# --- MAIN ---

train()
