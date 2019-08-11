function buildModelMain(){

  //saveInputData();
  // $("#container").empty();

  var sub_title = "Building your Keras model ...";
  mainTitleInit(sub_title);

  /*var trainX, trainY, testX, testY =*/ //getDataset();
  // var model = getModel();
  // compileModel(model);
  // trainModel();
  // saveModel();
  // if(evaluate_question) evaluateModel();

  // prova();
  //examples();
}

function getDataset() {

  // var dataset_json = JSON.parse(file_dataset);
  // console.log(dataset_json);
  // console.log(dataset_json.trainX);

  $("#up").change(function(event){
  	var uploadedFile = event.target.files[0];

    var readFile = new FileReader();
    readFile.onload = function(e) {
      var contents = e.target.result;
      var json = JSON.parse(contents);
      console.log(json);
    };
    readFile.readAsText(uploadedFile);
  });

}


function saveInputData(){
  file_dataset = $("#import_dataset").prop('files');
  console.log(file_dataset.result);

  $("#import_dataset").change(function(event){
  	var uploadedFile = event.target.files[0];

    var readFile = new FileReader();
    readFile.onload = function(e) {
      var contents = e.target.result;
      var json = JSON.parse(contents);
      console.log(json);
    };
    readFile.readAsText(uploadedFile);
  });

}

function prova() {
  // Create a rank-2 tensor (matrix) matrix tensor from a multidimensional array.
  const a = tf.tensor([[1, 1, 1], [2, 2, 2]]);
  console.log('shape:', a.shape);
  a.print();

  // Or you can create a tensor from a flat array and specify a shape.
  const shape = [1, 4];
  const b = tf.tensor([1, 2, 3, 4], shape);
  console.log('shape:', b.shape);
  b.print();
}


function examples() {

  // Define a model for linear regression.
  const model = tf.sequential();
  model.add(tf.layers.dense({units: 1, inputShape: [3]}));
  model.add(tf.layers.dense({units: 10, activation: "relu"}));
  model.add(tf.layers.dense({units: 1, activation: "softmax"}));


  // var myOptim = tf.train.sgd({lr: 0.2});
  model.compile({loss: 'meanSquaredError', optimizer: 'sgd' /* myOptim, metrics:["accuracy"]*/});

  // Generate some synthetic data for training.
  const xs = tf.tensor2d([[1,1,1], [2,2,2], [3,3,3], [4,4,4]], [4,3]);   //[4 elems, 1 ??] [x_train.len, x_train[0].len ??]
  const ys = tf.tensor2d([3, 6, 9, 12], [4, 1]); //[y_train.len, y_train[0].len ??]
  console.log(xs.shape);
  xs.print();
  console.log(ys.shape);
  ys.print();


  // Train the model using the data.
  /* await */ model.fit(xs, ys, {epochs: 10 /*, batchSize, verbose, validationSplit */}).then(() => {
    /*model.evaluate(x_test, y_test);*/model.predict(tf.ones([5,5,5])).print();
  });

  // Use the model to do inference on a data point the model hasn't seen before:
  model.predict(tf.ones([5,5,5])).print();

}
