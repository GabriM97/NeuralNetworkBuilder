import sys
import numpy as np
import re
from keras.models import load_model

def importModel():
    filename = "./python/saves/client_model.h5"     # PHP SCRIPT
    #filename = "./saves/client_model.h5"            # CMD SRIPT
    model = -1
    model = load_model(filename)
    if(model == -1):
        print("Error importing the model.")

    return model

# -------------------------------------------------

def test():
    testList = []
    test_x = list(map(float, sys.argv[1].split(",")))
    testList.append(test_x)
    print("input:", testList)

    model = importModel()
    prediction = -1
    prediction = model.predict(testList)
    # print("\nPrediction:", np.argmax(prediction, axis=1))
    print("\nPrediction:\n", prediction)
    if(prediction == -1):
        print("exit_status:", prediction)
    else:
        print("exit_status: 0")

# --- MAIN ---

test()
