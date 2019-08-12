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

    print("\nModel compiled!")
    return model


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

#--------------------------------------------------------
def compile():

    model = importModel()
    optimizer = sys.argv[1]
    learning_rate = int(sys.argv[2])
    output_classes = int(sys.argv[3])
    #metrics_list = sys.argv[4]
    metrics_list = ["accuracy"]

    model = compileModel(model, optimizer, learning_rate, output_classes, metrics_list)
    exit = saveModel(model)
    print("exit_status:", exit)

# --- MAIN ---
compile()
