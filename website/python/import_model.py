import sys
import numpy as np
from keras.models import load_model

def importModel():
    #filename = "./python/saves/personal_model.h5"     # PHP SCRIPT
    filename = "./saves/personal_model.h5"            # CMD SRIPT
    model = -1
    model = load_model(filename)
    if(model == -1):
        print("Error importing the model.")

    return model

# -------------------------------------------------

def test():
    testList = []
    test_x = int(sys.argv[1])
    testList.append(test_x)
    model = importModel()
    prediction = model.predict(testList)
    # print("\nPrediction:", np.argmax(prediction, axis=1))
    print("\nPrediction:\n", prediction)

# --- MAIN ---

test()
