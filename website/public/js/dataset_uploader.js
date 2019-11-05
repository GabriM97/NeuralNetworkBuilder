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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/dataset_uploader.js":
/*!******************************************!*\
  !*** ./resources/js/dataset_uploader.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  storeDatasetUrl = document.referrer;
  $("#dataset_file").change(function () {
    var filename = $('#dataset_file')[0].files[0].name;
    $('label[for="dataset_file"]').html(filename);
  });
  var pbar = $('#progressBar'),
      currentProgress = 0;

  function trackUploadProgress(e) {
    if (e.lengthComputable) {
      currentProgress = e.loaded / e.total * 100; // Amount uploaded in percent

      $(pbar).width(currentProgress + '%'); //if (currentProgress == 100)	console.log('Progress : 100%');
    }
  }

  function uploadFile() {
    var formdata = new FormData($('#main-form')[0]);
    $.ajax({
      url: storeDatasetUrl,
      type: 'post',
      data: formdata,
      xhr: function xhr() {
        // Custom XMLHttpRequest
        var appXhr = $.ajaxSettings.xhr(); // Check if upload property exists, if "yes" then upload progress can be tracked otherwise "not"

        if (appXhr.upload) {
          // Attach a function to handle the progress of the upload
          appXhr.upload.addEventListener('progress', trackUploadProgress, false);
        }

        return appXhr;
      },
      success: function success(data) {
        window.location.replace(data);
      },
      error: function error(reject) {
        $('#progressBar').addClass("d-none");
        $("#upload-button").removeClass("disabled").attr("disabled", false);
        var response = $.parseJSON(reject.responseText);

        if (reject.status === 422) {
          $("span.invalid-feedback").remove();
          $.each(response.errors, function (key, val) {
            $("#" + key).addClass("is-invalid");
            $("#" + key).after('<span class="invalid-feedback" role="alert"><strong>' + val + '</strong></span>');
          });
        } else {
          $("h2").after('<span class="invalid-feedback" role="alert"><strong>' + response.message + '</strong></span>');
        }
      },
      // Tell jQuery "Hey! don't worry about content-type and don't process the data"
      // These two settings are essential for the application
      contentType: false,
      processData: false
    });
  }

  $("#main-form").submit(function (e) {
    e.preventDefault();
    $(pbar).width(0).removeClass('d-none').addClass('active');
    $("#upload-button").addClass("disabled").attr("disabled", true);
    uploadFile();
  });
});

/***/ }),

/***/ 4:
/*!************************************************!*\
  !*** multi ./resources/js/dataset_uploader.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /home/gabri/Desktop/NeuralNetworkBuilder/nnb_website/resources/js/dataset_uploader.js */"./resources/js/dataset_uploader.js");


/***/ })

/******/ });