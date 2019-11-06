import sys
import pickle
import json
import numpy as np
import pandas as pd  #for csv files

def getExampleDataset():
    import mnist

    train_images = mnist.train_images()[1:100]
    train_labels = mnist.train_labels()[1:100]
    test_images = mnist.test_images()[1:100]
    test_labels = mnist.test_labels()[1:100]

    # Normalize images: values from [0, 255] to [-0.5, 0.5]
    train_images = (train_images / 255) - 0.5
    test_images = (test_images / 255) - 0.5

    # Resize images into 784 dimensions vector
    train_images = train_images.reshape((-1,784))
    test_images = test_images.reshape((-1,784))

    print("\nMNIST Dataset loaded!")
    return train_images, train_labels, test_images, test_labels


def saveDatasetPKL(data):
    path = "./saves/"
    filename = path + "local_dataset.pkl"

    try:
        with open(filename, 'wb') as inp:
            pickle.dump(data, inp)
            print("\nLocal dataset (PKL) saved.")
            return 0
    except IOError as e:
        print("\nError saving local dataset: " + e)
        return -1


def saveDatasetJSON(data):
    path = "./saves/"
    filename = path + "local_dataset.json"
    data = checkType(data)

    try:
        with open(filename, 'w') as inp:
            json.dump(data, inp)
            print("\nLocal dataset (JSON) saved.")
            return 0
    except IOError as e:
        print("\nError saving local dataset: " + e)
        return -1


def saveDatasetCSV(data):
    path = "./saves/"
    filename = path + "local_dataset.csv"
    data = checkType(data)

    try:
        data = pd.DataFrame.from_dict(data, orient='index')
        data.transpose().to_csv(filename)
        print("\nLocal dataset (CSV) saved.")
        return 0
    except IOError as e:
        print("\nError saving local dataset: " + e)
        return -1


def loadLocalDataset(filename):
    path = "./saves/" + filename
    try:
        if(filename.find(".pkl", -5) != -1):
            with open(path, "rb") as inp:
                data = pickle.load(inp)
        elif(filename.find(".json", -6) != -1):
            with open(path, "r") as inp:
                data = json.load(inp)
        elif(filename.find(".csv", -5) != -1):
            data = pd.read_csv(path)
            print("\nLocal Dataset", filename, "loaded!")
            train_x = data["train_x"].tolist()
            train_y = data["train_y"].tolist()
            test_x = data["test_x"].tolist()
            test_y = data["test_y"].tolist()
            return train_x, train_y, test_x, test_y

        print("\nLocal Dataset", filename, "loaded!")
    except Exception as e:
        print("\n", e, "Error trying to Load data from", filename)
        return -1, -1, -1, -1

    train_x = data["train_x"]
    train_y = data["train_y"]
    test_x = data["test_x"]
    test_y = data["test_y"]

    return train_x, train_y, test_x, test_y


def checkType(data):
    train_x = data["train_x"]
    train_y = data["train_y"]
    test_x = data["test_x"]
    test_y = data["test_y"]

    if(isinstance(train_x, np.ndarray)): train_x = train_x.tolist()
    if(isinstance(train_y, np.ndarray)): train_y = train_y.tolist()
    if(isinstance(test_x, np.ndarray)): test_x = test_x.tolist()
    if(isinstance(test_y, np.ndarray)): test_y = test_y.tolist()

    data = {"train_x": train_x,
            "train_y": train_y,
            "test_x": test_x,
            "test_y": test_y }
    return data

# --------------------------------------------------------
def get_MNIST_save():
    train_x, train_y, test_x, test_y = getExampleDataset()
    data = {"train_x": train_x,
            "train_y": train_y,
            "test_x": test_x,
            "test_y": test_y }

    saveDatasetPKL(data)
    #saveDatasetJSON(data)
    #saveDatasetCSV(data)

# --------------------------------------------------------
def load_local(filename="saves/local_dataset.pkl"):
    train_x, train_y, test_x, test_y = loadLocalDataset(filename)
    #print("\ntrain_x:", train_x,"\ntrain_y:", train_y, "\ntest_x:", test_x, "\ntest_y:", test_y)

    data = {"train_x": train_x,
            "train_y": train_y,
            "test_x": test_x,
            "test_y": test_y }

    #saveDatasetPKL(data)
    saveDatasetJSON(data)
    #saveDatasetCSV(data)

# --- MAIN ---

get_MNIST_save()
#load_local(sys.argv[1])
