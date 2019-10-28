# -*- coding: utf-8 -*-
"""
    - Neural Network Builder -
    @author: GabriM97
"""

import numpy as np
import pickle
from keras.models import Sequential
from keras.layers import Dense
from keras.utils import to_categorical


def saveModelWeights(model, filename):
    try:
        model.save_weights(filename)
        print("\nModel's weights saved!")
    except IOError:
        print("\nError saving the model's weights.")
        return -1
    return 0


def loadModelWeights(model, filename):
    model.load_weights(filename)
    print("\nWeights loaded!")
    return model


def evaluateModel(model, x, y, metrics_list):
    print("\nEvaluating the Model:")
    loss = model.evaluate(x, y)

    print("\nLoss:", round(loss[0], 4))
    i=1
    for metric in metrics_list:
        print(metric + ":", loss[i])
        i+=1


def trainModel(model, x, y, num_epochs, batch_dim=32, verb=0, valid_split=0.0):
    print("\nStart to train the model!")
    model.fit(x, y,
              epochs=num_epochs,
              batch_size=batch_dim,
              verbose=verb,
              validation_split=valid_split)

    print("\nTraining completed.")


def compileModel(model, optim, output_classes, metrics_list):
    if(output_classes > 2):
        loss_func = "categorical_crossentropy"
    else:
        loss_func = "binary_crossentropy"

    model.compile(optimizer=optim, loss=loss_func, metrics=metrics_list)
    print("\nModel compiled. Ready for training!")


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


def loadExampleDataset():
    import mnist

    train_images = mnist.train_images()
    train_labels = mnist.train_labels()
    test_images = mnist.test_images()
    test_labels = mnist.test_labels()

    # Normalize images: values from [0, 255] to [-0.5, 0.5]
    train_images = (train_images / 255) - 0.5
    test_images = (test_images / 255) - 0.5

    # Resize images into 784 dimensions vector
    train_images = train_images.reshape((-1,784))
    test_images = test_images.reshape((-1,784))

    print("\nMNIST Dataset loaded!")
    #print(train_images[0])
    #print(train_images.shape)
    #print(test_images.shape)

    return train_images, train_labels, test_images, test_labels


def loadLocalDataset(filename):
    try:
        with open(filename, "rb") as inp:
            data = pickle.load(inp)
            print("\nLocal Dataset loaded!")

    except IOError:
        print("\nError trying to Load data from", filename)
        return -1

    train_x = data["train_x"]
    train_y = data["train_y"]
    test_x = data["test_x"]
    test_y = data["test_y"]

    return train_x, train_y, test_x, test_y


def create_and_save_NewModel():
    train_x, train_y, test_x, test_y = loadLocalDataset("./saves/local_dataset.pkl")
    #train_x, train_y, test_x, test_y = loadExampleDataset()

    layers_number = 3
    output_classes = 10
    neurons_per_layer = [64,64,output_classes]
    activ_functions = ["relu", "relu", "softmax"]
    data_shape = train_x[0].shape
    model_type = "Seq"      #Sequential model
    get_info = True

    model = buildModel(layers_number,
                       neurons_per_layer,
                       activ_functions,
                       data_shape,
                       model_type,
                       get_info)

    #learning_rate = 0.005
    #optimizer = Adam(lr=learning_rate)
    optimizer = "adam"
    metrics_list = ["accuracy"]
    compileModel(model, optimizer, output_classes, metrics_list)

    if(output_classes > 2):
        train_y = to_categorical(train_y)
        test_y = to_categorical(test_y)

    epochs = 5
    batch_size = 64
    verbose = 2
    valid_split = 0.4
    trainModel(model, train_x, train_y, epochs, batch_size, verbose, valid_split)

    saveModelWeights(model, "./model_mnist.h5")

    evaluateModel(model, test_x, test_y, metrics_list)

    print("exit_status: 0")


def load_Model():
    train_x, train_y, test_x, test_y = loadExampleDataset()

    layers_number = 3
    output_classes = 10
    neurons_per_layer = [64,64,output_classes]
    activ_functions = ["relu", "relu", "softmax"]
    data_shape = train_x[0].shape
    model_type = "Seq"      #Sequential model
    get_info = False

    model = buildModel(layers_number,
                       neurons_per_layer,
                       activ_functions,
                       data_shape,
                       model_type,
                       get_info)

    loadModelWeights(model, "./model_mnist.h5")

    prediction = model.predict(test_x[:5])   #predict the first 5 images
    print("\nPrediction:", np.argmax(prediction, axis=1))
    #print("\nPrediction:\n", prediction)
    print("\nTruth:", test_y[:5])

# --- MAIN ---

create_and_save_NewModel()
#load_Model()
