import sys
import pickle

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

    return train_images, train_labels, test_images, test_labels


def saveExampleDataset(data):
    try:
        with open("local_dataset.pkl", 'wb') as inp:
            pickle.dump(data, inp)
            print("\nLocal dataset saved.")
            return 0
    except IOError:
        print("\nError saving local dataset.")
        return -1


def loadLocalDataset(filename):
    try:
        with open(filename, "rb") as inp:
            data = pickle.load(inp)
            print("\nLocal Dataset", filename, "loaded!")
    except IOError:
        print("\nError trying to Load data from", filename)
        return -1

    train_x = data["train_x"]
    train_y = data["train_y"]
    test_x = data["test_x"]
    test_y = data["test_y"]

    return train_x, train_y, test_x, test_y

# --------------------------------------------------------
def get_MNIST_save():
    train_x, train_y, test_x, test_y = loadExampleDataset()
    data = {"train_x": train_x,
            "train_y": train_y,
            "test_x": test_x,
            "test_y": test_y }
    saveExampleDataset(data)

def load_local(filename):
    train_x, train_y, test_x, test_y = loadLocalDataset(filename)
    print(train_x, train_y, test_x, test_y)

# --- MAIN ---

#get_MNIST_save()
filename = sys.argv[1]
load_local(filename)
