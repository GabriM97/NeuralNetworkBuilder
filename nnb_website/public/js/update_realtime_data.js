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
$(document).ready(function () {
  var updateDataUrl = window.location.href + "/getTrainingInfo";
  if ($("#in_queue").attr("value") === "1") //if training is processing
    interval = setInterval(getData, 500, updateDataUrl);
});

function getData(updateDataUrl) {
  //var current_train_perc = $("#train_perc").text();
  //var current_acc_val = $("#acc_val").text();
  //var current_loss_val = $("#loss_val").text();
  var csrf_token = $("input[name='_token']").val();
  fetch(updateDataUrl, {
    method: 'post',
    headers: {
      'Content-Type': 'application/json',
      // sent request
      'Accept': 'application/json' // expected data sent back

    },
    body: JSON.stringify({
      _token: csrf_token
    })
  }).then(function (res) {
    return res.json();
  }).then(function (data) {
    return setData(data);
  })["catch"](function (error) {
    return console.log(error);
  });

  if ($("#in_queue").attr("value") === "0") {
    clearInterval(interval);
    location.reload(true);
  }
}

function setData(data) {
  $("#in_queue").attr("value", data["in_queue"]);
  $("#train_status").text(getStatus(data["status"]));
  $("#train_perc").text(data["train_perc"] * 100);
  $("#acc_val").text(data["accuracy"] * 100);
  $("#loss_val").text(data["loss"] * 100);
}

function getStatus(status) {
  if (status == "started") {
    $("#train_status").parent().attr("class", "").addClass("text-primary");
    return "In Progress";
  } else if (status == "paused") {
    $("#train_status").parent().attr("class", "").addClass("text-info");
    return "In Pause";
  }
}

/***/ }),

/***/ 3:
/*!****************************************************!*\
  !*** multi ./resources/js/update_realtime_data.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/gabri/Desktop/NeuralNetworkBuilder/nnb_website/resources/js/update_realtime_data.js */"./resources/js/update_realtime_data.js");


/***/ })

/******/ });