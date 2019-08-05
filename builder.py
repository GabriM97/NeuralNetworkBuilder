# -*- coding: utf-8 -*-
"""
    - Neural Network Builder -
    @author: GabriM97
"""

import numpy as np
import pickle
from keras.models import Sequential
from keras.layers import Dense


def trainModel(optim):
    print()
    
    

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
    
    train_x = data[0]   #TRAIN X
    train_y = data[1]   #TRAIN Y
    test_x = data[2]    #TEST X
    test_y = data[3]    #TEST Y
    
    return train_x, train_y, test_x, test_y



# --- MAIN ---

#train_x, train_y, test_x, test_y = loadLocalDataset()
train_x, train_y, test_x, test_y = loadExampleDataset()


layers_number = 3
neurons_per_layer = [64,64,10]
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























