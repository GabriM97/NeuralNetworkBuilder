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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/manage_training.js":
/*!*****************************************!*\
  !*** ./resources/js/manage_training.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  disableDataset($("#model_id"), true);
  $("#model_id").change(function () {
    disableDataset(this, false); //if not exists dataset training selected option

    if (!$("#training_dataset option:selected").length) {
      /*setTimeout(function() { // render HTML before run the alert
          alert("No matching dataset found! \nImport a new matching dataset or change the model.");
          },0);*/
      error_msg = '\
            <span class="invalid-feedback" role="alert">\
                <strong>No matching dataset found! Import a new matching dataset or change the model.</strong>\
            </span>'; //$("#training_dataset").after(error_msg);
    }
  });
});

function disableDataset(model, first_run) {
  var selectedModel = $(model).children("option:selected");
  x = $(selectedModel).attr("x_inp");
  y = $(selectedModel).attr("y_out"); //for each training dataset option

  $("#training_dataset option").map(function () {
    x_data = $(this).attr("x_inp");
    y_data = $(this).attr("y_out");

    if (x_data != x || y_data != y) {
      $(this).attr("disabled", true).addClass('d-none');
      $(this).attr('selected', false);
    } else {
      // x_data == x && y_data == y
      if (!$('input[name="_method"]').length) {
        // new training
        $("#training_dataset").children("option:selected").attr('selected', false); //remove previously selected option

        $(this).attr("disabled", false).removeClass('d-none');
        $(this).attr('selected', true);
      } else {
        // edit training
        if (first_run) $(this).attr("disabled", false).removeClass('d-none');else {
          // model on change
          $("#training_dataset").children("option:selected").attr('selected', false); //remove previously selected option

          $(this).attr("disabled", false).removeClass('d-none');
          $(this).attr('selected', true);
        }
      }
    }
  }); //for each test dataset option

  $("#test_dataset option").map(function () {
    if (!$(this).val()) return;
    x_data = $(this).attr("x_inp");
    y_data = $(this).attr("y_out");

    if (x_data != x || y_data != y) {
      $(this).attr("disabled", true).addClass('d-none');
      $(this).attr('selected', false);
    } else {
      // x_data == x && y_data == y
      if (!$('input[name="_method"]').length) {
        // new training
        $("#test_dataset").children("option:selected").attr('selected', false); //remove previously selected option

        $(this).attr("disabled", false).removeClass('d-none');
        $(this).attr('selected', true);
      } else {
        // edit training
        if (first_run) $(this).attr("disabled", false).removeClass('d-none');else {
          // model on change
          $("#test_dataset").children("option:selected").attr('selected', false); //remove previously selected option

          $(this).attr("disabled", false).removeClass('d-none');
          $(this).attr('selected', true);
        }
      }
    }
  });
}

/***/ }),

/***/ 2:
/*!***********************************************!*\
  !*** multi ./resources/js/manage_training.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/gabri/Desktop/NeuralNetworkBuilder/nnb_website/resources/js/manage_training.js */"./resources/js/manage_training.js");


/***/ })

/******/ });