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
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "../src/assets/js/pages/components/keen-datatable/base/data-ajax.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "../src/assets/js/pages/components/keen-datatable/base/data-ajax.js":
/*!**************************************************************************!*\
  !*** ../src/assets/js/pages/components/keen-datatable/base/data-ajax.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\r\n// Class definition\r\n\r\nvar KTDatatableRemoteAjaxDemo = function() {\r\n\t// Private functions\r\n\r\n\t// basic demo\r\n\tvar demo = function() {\r\n\r\n\t\tvar datatable = $('.kt_datatable').KTDatatable({\r\n\t\t\t// datasource definition\r\n\t\t\tdata: {\r\n\t\t\t\ttype: 'remote',\r\n\t\t\t\tsource: {\r\n\t\t\t\t\tread: {\r\n\t\t\t\t\t\turl: 'https://keenthemes.com/keen/tools/preview/inc/api/datatables/demos/default2.php',\r\n\t\t\t\t\t\tmap: function(raw) {\r\n\t\t\t\t\t\t\t// sample data mapping\r\n\t\t\t\t\t\t\tvar dataSet = raw;\r\n\t\t\t\t\t\t\tif (typeof raw.data !== 'undefined') {\r\n\t\t\t\t\t\t\t\tdataSet = raw.data;\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\treturn dataSet;\r\n\t\t\t\t\t\t},\r\n\t\t\t\t\t},\r\n\t\t\t\t},\r\n\t\t\t\tpageSize: 10,\r\n\t\t\t\tserverPaging: true,\r\n\t\t\t\tserverFiltering: true,\r\n\t\t\t\tserverSorting: true,\r\n\t\t\t},\r\n\r\n\t\t\t// layout definition\r\n\t\t\tlayout: {\r\n\t\t\t\tscroll: false,\r\n\t\t\t\tfooter: false,\r\n\t\t\t},\r\n\r\n\t\t\t// column sorting\r\n\t\t\tsortable: true,\r\n\r\n\t\t\tpagination: true,\r\n\r\n\t\t\tsearch: {\r\n\t\t\t\tinput: $('#generalSearch'),\r\n\t\t\t},\r\n\r\n\t\t\t// columns definition\r\n\t\t\tcolumns: [\r\n\t\t\t\t{\r\n\t\t\t\t\tfield: 'id',\r\n\t\t\t\t\ttitle: '#',\r\n\t\t\t\t\tsortable: false,\r\n\t\t\t\t\twidth: 30,\r\n\t\t\t\t\ttype: 'number',\r\n\t\t\t\t\tselector: false,\r\n\t\t\t\t\ttextAlign: 'center',\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'employee_id',\r\n\t\t\t\t\ttitle: 'Employee ID',\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'name',\r\n\t\t\t\t\ttitle: 'Name',\r\n\t\t\t\t\ttemplate: function(row) {\r\n\t\t\t\t\t\treturn row.first_name + ' ' + row.last_name;\r\n\t\t\t\t\t},\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'hire_date',\r\n\t\t\t\t\ttitle: 'Hire Date',\r\n\t\t\t\t\ttype: 'date',\r\n\t\t\t\t\tformat: 'MM/DD/YYYY',\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'gender',\r\n\t\t\t\t\ttitle: 'Gender',\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'status',\r\n\t\t\t\t\ttitle: 'Status',\r\n\t\t\t\t\t// callback function support for column rendering\r\n\t\t\t\t\ttemplate: function(row) {\r\n\t\t\t\t\t\tvar status = {\r\n\t\t\t\t\t\t\t1: {'title': 'Pending', 'class': 'kt-badge--brand'},\r\n\t\t\t\t\t\t\t2: {'title': 'Delivered', 'class': ' kt-badge--metal'},\r\n\t\t\t\t\t\t\t3: {'title': 'Canceled', 'class': ' kt-badge--primary'},\r\n\t\t\t\t\t\t\t4: {'title': 'Success', 'class': ' kt-badge--success'},\r\n\t\t\t\t\t\t\t5: {'title': 'Info', 'class': ' kt-badge--info'},\r\n\t\t\t\t\t\t\t6: {'title': 'Danger', 'class': ' kt-badge--danger'},\r\n\t\t\t\t\t\t\t7: {'title': 'Warning', 'class': ' kt-badge--warning'},\r\n\t\t\t\t\t\t};\r\n\t\t\t\t\t\treturn '<span class=\"kt-badge ' + status[row.status].class + ' kt-badge--inline kt-badge--pill\">' + status[row.status].title + '</span>';\r\n\t\t\t\t\t},\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'type',\r\n\t\t\t\t\ttitle: 'Type',\r\n\t\t\t\t\tautoHide: false,\r\n\t\t\t\t\t// callback function support for column rendering\r\n\t\t\t\t\ttemplate: function(row) {\r\n\t\t\t\t\t\tvar status = {\r\n\t\t\t\t\t\t\t1: {'title': 'Online', 'state': 'danger'},\r\n\t\t\t\t\t\t\t2: {'title': 'Retail', 'state': 'primary'},\r\n\t\t\t\t\t\t\t3: {'title': 'Direct', 'state': 'accent'},\r\n\t\t\t\t\t\t};\r\n\t\t\t\t\t\treturn '<span class=\"kt-badge kt-badge--' + status[row.type].state + ' kt-badge--dot\"></span>&nbsp;<span class=\"kt-font-bold kt-font-' + status[row.type].state + '\">' +\r\n\t\t\t\t\t\t\t\tstatus[row.type].title + '</span>';\r\n\t\t\t\t\t},\r\n\t\t\t\t}, {\r\n\t\t\t\t\tfield: 'Actions',\r\n\t\t\t\t\ttitle: 'Actions',\r\n\t\t\t\t\tsortable: false,\r\n\t\t\t\t\twidth: 110,\r\n\t\t\t\t\toverflow: 'visible',\r\n\t\t\t\t\tautoHide: false,\r\n\t\t\t\t\ttemplate: function() {\r\n\t\t\t\t\t\treturn '\\\r\n\t\t\t\t\t\t<div class=\"dropdown\">\\\r\n\t\t\t\t\t\t\t<a href=\"javascript:;\" class=\"btn btn-sm btn-clean btn-icon btn-icon-sm\" data-toggle=\"dropdown\">\\\r\n                                <i class=\"flaticon2-gear\"></i>\\\r\n                            </a>\\\r\n\t\t\t\t\t\t  \t<div class=\"dropdown-menu dropdown-menu-right\">\\\r\n\t\t\t\t\t\t    \t<a class=\"dropdown-item\" href=\"#\"><i class=\"la la-edit\"></i> Edit Details</a>\\\r\n\t\t\t\t\t\t    \t<a class=\"dropdown-item\" href=\"#\"><i class=\"la la-leaf\"></i> Update Status</a>\\\r\n\t\t\t\t\t\t    \t<a class=\"dropdown-item\" href=\"#\"><i class=\"la la-print\"></i> Generate Report</a>\\\r\n\t\t\t\t\t\t  \t</div>\\\r\n\t\t\t\t\t\t</div>\\\r\n\t\t\t\t\t\t<a href=\"javascript:;\" class=\"btn btn-sm btn-clean btn-icon btn-icon-sm\" title=\"Edit details\">\\\r\n\t\t\t\t\t\t\t<i class=\"flaticon2-paper\"></i>\\\r\n\t\t\t\t\t\t</a>\\\r\n\t\t\t\t\t\t<a href=\"javascript:;\" class=\"btn btn-sm btn-clean btn-icon btn-icon-sm\" title=\"Delete\">\\\r\n\t\t\t\t\t\t\t<i class=\"flaticon2-trash\"></i>\\\r\n\t\t\t\t\t\t</a>\\\r\n\t\t\t\t\t';\r\n\t\t\t\t\t},\r\n\t\t\t\t}],\r\n\r\n\t\t});\r\n\r\n    $('#kt_form_status').on('change', function() {\r\n      datatable.search($(this).val().toLowerCase(), 'status');\r\n    });\r\n\r\n    $('#kt_form_type').on('change', function() {\r\n      datatable.search($(this).val().toLowerCase(), 'type');\r\n    });\r\n\r\n    $('#kt_form_status,#kt_form_type').selectpicker();\r\n\r\n\t};\r\n\r\n\treturn {\r\n\t\t// public functions\r\n\t\tinit: function() {\r\n\t\t\tdemo();\r\n\t\t},\r\n\t};\r\n}();\r\n\r\njQuery(document).ready(function() {\r\n\tKTDatatableRemoteAjaxDemo.init();\r\n});\n\n//# sourceURL=webpack:///../src/assets/js/pages/components/keen-datatable/base/data-ajax.js?");

/***/ })

/******/ });