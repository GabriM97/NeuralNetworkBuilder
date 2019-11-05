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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/generate_layers.js":
/*!*****************************************!*\
  !*** ./resources/js/generate_layers.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  addLayers();
});

function addLayers() {
  // GET LAYERS NUMBER
  var layers_num = $("#layers_number").val(); //Listener on input

  $(document).on('input', '#layers_number', function () {
    var layers_num = $(this).val();
    renderLayers(layers_num);
  });
  renderLayers(layers_num);
}

var content_cnt = 0;

function renderLayers(layers_num) {
  $("#layers_container").empty();
  content_cnt = 0; // comment?

  var layers_per_row = 4;
  var cols_full = 12 / layers_per_row;
  var num_of_main_rows = layers_num > layers_per_row ? parseInt(layers_num / layers_per_row) + 1 : 1;
  var last_row_layers = layers_num % layers_per_row;
  if (last_row_layers != 0) var cols_last_layer = 12 / last_row_layers;else var cols_last_layer = cols_full;
  var main_row_cnt = 0;
  $("#layers_container").append("<div id='layers-row_" + main_row_cnt + "' class='row'></div>");
  main_row_cnt++;

  for (var i = 0; i < layers_num; i++) {
    if (main_row_cnt == num_of_main_rows) {
      // last row
      addLayer_col(cols_last_layer, main_row_cnt - 1, i);
    } else {
      // NOT last row
      addLayer_col(cols_full, main_row_cnt - 1, i);

      if ((i + 1) % layers_per_row == 0 && i != layers_num - 1) {
        //next step is new row
        $("#layers_container").append("<div id='layers-row_" + main_row_cnt + "' class='row'></div>");
        main_row_cnt++;
      }
    }
  } // sync output classes with last layer neurons


  var last_layer_selector = "#layers_container [id*='_" + (content_cnt - 1) + "'] [name*='neurons_number']";
  var out_val = $("#output_classes").val();
  $(last_layer_selector).val(out_val);
  $("#output_classes").change(function () {
    var value = $(this).val();
    var last_layer_selector = "#layers_container [id*='_" + (content_cnt - 1) + "'] [name*='neurons_number']";
    $(last_layer_selector).val(value);
  });
  $(last_layer_selector).change(function () {
    var value = $(this).val();
    $("#output_classes").val(value);
  });
}

function addLayer_col(cols, current_main_row, layer_number_cnt) {
  var layer_container = $("<div class='main-container border border-secondary my-4 p-4 rounded-pill col-" + cols + "'></div>");
  $(layer_container).append(""); //Layer number

  $(layer_container).append("<div class='row'><div class='col text-center font-weight-bold'><i class='fas fa-layer-group fa-sm pr-2'></i>Layer " + layer_number_cnt + "</div></div>"); // Container Row for Neurons and Activ Function

  $(layer_container).append("<div id='row-" + current_main_row + "_" + content_cnt + "' class='row'></div>");
  content_cnt++;
  $("#layers-row_" + current_main_row).append(layer_container); // Neurons number

  var neurons_num = '\
  	<div class="col-md text-center">\
  		<label class="col-form-label font-weight-bold" for="neurons_number[]">Neurons</label>\
  		<input class="form-control" type="number" name="neurons_number[]" min="1" max="500" value="4">\
	</div>';
  $("#row-" + current_main_row + "_" + (content_cnt - 1)).append(neurons_num); // Activ function

  var activ_func = '\
	<div class="col-md text-center">\
		<label class="col-form-label font-weight-bold" for="activ_funct[]">Activ. function</label>\
		<select class="form-control" name="activ_funct[]">\
			<option value="relu" selected="">ReLU</option>\
			<option value="sigmoid">Sigmoid</option>\
			<option value="tanh">Tanh</option>\
			<option value="linear">Linear</option>\
			<option value="softmax">Softmax</option>\
		</select>\
	</div>';
  $("#row-" + current_main_row + "_" + (content_cnt - 1)).append(activ_func);
}

/***/ }),

/***/ 1:
/*!***********************************************!*\
  !*** multi ./resources/js/generate_layers.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/gabri/Desktop/NeuralNetworkBuilder/nnb_website/resources/js/generate_layers.js */"./resources/js/generate_layers.js");


/***/ })

/******/ });