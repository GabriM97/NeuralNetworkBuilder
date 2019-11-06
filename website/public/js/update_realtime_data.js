/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/update_realtime_data.js":
/*!**********************************************!*\
  !*** ./resources/js/update_realtime_data.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var interval;
var chartConfig = {
  type: 'line',
  data: {
    labels: [],
    datasets: [{
      // ACCURACY
      label: 'Accuracy',
      backgroundColor: 'rgba(100, 50, 50, 0)',
      borderColor: 'rgba(255, 50, 50, 1)',
      data: [],
      fill: false
    }, {
      // LOSS
      label: 'Loss',
      backgroundColor: 'rgba(50, 50, 100, 0)',
      borderColor: 'rgba(50, 50, 255, 1)',
      data: [],
      fill: false
    }]
  },
  options: {
    scales: {
      xAxes: [{
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'Epochs'
        }
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true
        },
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'Accuracy / Loss'
        }
      }]
    },
    legend: {//position: right,
    },
    responsive: true,
    maintainAspectRatio: false,
    aspectRatio: 2
  }
};
$(document).ready(function () {
  epochs_array = new Array();

  for (i = 0; i < $("#epochs").text(); i++) {
    epochs_array[i] = i;
  } // RENDER CHART
  //chartConfig.data.labels = epochs_array;


  var ctx = document.getElementById('training_chart').getContext('2d');
  window.trainingChart = new Chart(ctx, chartConfig); // UPDATE TRAINING VALUES

  var updateDataUrl = window.location.href + "/getTrainingInfo";
  if ($("#in_queue").attr("value") === "1") //if training is processing
    interval = setInterval(getData, 500, updateDataUrl);
  $(".training-main-form").submit(function (e) {
    //e.preventDefault();
    var formType = $(".training-main-form input[name='_type']").val();
    var trainingStatus = $("#train_status").attr("value");

    if ((formType == "stop" || formType == "pause") && trainingStatus == "started") {
      downloadCanvasImage();
    }

    return true;
  });
});

function getData(updateDataUrl) {
  var csrf_token = $("input[name='_token']").val();
  fetch(updateDataUrl, {
    method: 'post',
    headers: {
      'Content-Type': 'application/json',
      // sent request
      'Accept': 'application/json' // expected data sent back

    },
    body: JSON.stringify({
      _token: csrf_token,
      _type: 'update_data'
    })
  }).then(function (res) {
    return res.json();
  }).then(function (data) {
    return setData(data);
  }).then(function (data) {
    return updateChart(data["accuracy"], data["loss"], data["epoch"]);
  })["catch"](function (error) {
    return console.log(error);
  });
}

function setData(data) {
  var prev_status = $("#train_status").attr("value");

  if (prev_status !== data["status"]) {
    if (prev_status == "started" && data["status"] == "stopped") {
      //training completed/stopped - Auto download canvas img
      downloadCanvasImage();
    }

    location.reload(true);
  }

  $("#in_queue").attr("value", data["in_queue"]);
  $("#train_status").attr("value", data["status"]);
  $("#train_perc").text(Math.round(data["train_perc"] * 100));
  $("#acc_val").text(Math.round(data["accuracy"] * 100));
  $("#loss_val").text(Math.round(data["loss"] * 100));

  if (data["evaluation_in_progress"] == 1) {
    $("#return-alert").text(data["return_message"]);
    $("#pause-resume-btn").addClass("d-none");
    $("#pause-resume-btn").attr("disabled", true);
  }

  return data;
}

last_epoch = -1;

function updateChart(accuracy_val, loss_val, epoch) {
  if ($("#train_status").attr("value") == "started" && epoch > last_epoch) {
    last_epoch = epoch; //var current_epoch_label = epochs_array[chartConfig.data.labels.length % epochs_array.length];

    chartConfig.data.labels.push(epoch);
    console.log(accuracy_val, loss_val);

    if (accuracy_val) {
      chartConfig.data.datasets[0].data.push(accuracy_val * 100);
    }

    chartConfig.data.datasets[1].data.push(loss_val * 100);
    window.trainingChart.update();
  }
}

function downloadCanvasImage() {
  var canvas = document.getElementById('training_chart');
  var imageUrl = canvas.toDataURL("image/jpeg");
  var link = document.createElement('a');
  link.href = imageUrl;
  link.download = "training_chart.jpg";
  link.style.display = "none";
  document.body.appendChild(link);
  link.click();
}

/***/ }),

/***/ 3:
/*!****************************************************!*\
  !*** multi ./resources/js/update_realtime_data.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/gabri/Desktop/NeuralNetworkBuilder/website/resources/js/update_realtime_data.js */"./resources/js/update_realtime_data.js");


/***/ })

/******/ });