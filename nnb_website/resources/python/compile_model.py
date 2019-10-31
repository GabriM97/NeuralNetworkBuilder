#!/usr/bin/python3

import sys
from keras import optimizers as opt
from keras.models import load_model

def compileModel(model, optim, learning_rate, output_classes, metrics_list):
    if(output_classes > 2):
        loss_func = "categorical_crossentropy"
    else:
        loss_func = "binary_crossentropy"

    if(optim == "adam"):
        optim_obj = opt.Adam(lr=learning_rate)
    elif(optim == "sgd"):
        optim_obj = opt.SGD(lr=learning_rate)

    model.compile(optimizer=optim_obj, loss=loss_func, metrics=metrics_list)

    return model
        
#--------------------------------------------------------

def compile():

    local_path = sys.argv[1]     # users/$hashed_user/models/model_ID.h5
    optimizer = sys.argv[2]
    learning_rate = float(sys.argv[3])
    output_classes = int(sys.argv[4])
    
    try:
        metrics_list = [sys.argv[5]]
    except Exception:
        metrics_list = None

    filename = "../storage/app/public/" + local_path
    try:
        model = load_model(filename)
        model = compileModel(model, optimizer, learning_rate, output_classes, metrics_list)
        model.save(filename)

    except Exception as err:
        print("COULD NOT COMIPLE THE MODEL - ERROR: " + str(err))
        raise err

# --- MAIN ---
compile()
