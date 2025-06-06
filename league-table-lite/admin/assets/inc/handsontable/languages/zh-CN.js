(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("../../handsontable"));
	else if(typeof define === 'function' && define.amd)
		define(["../../handsontable"], factory);
	else {
		var a = typeof exports === 'object' ? factory(require("../../handsontable")) : factory(root["Handsontable"]);
		for(var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
	}
})(typeof self !== 'undefined' ? self : this, function(__WEBPACK_EXTERNAL_MODULE_0__) {
return /******/ (function(modules) { // webpackBootstrap
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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 15);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_0__;

/***/ }),

/***/ 15:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;
exports.default = void 0;

var _handsontable = _interopRequireDefault(__webpack_require__(0));

var _dictionary;

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var C = _handsontable.default.languages.dictionaryKeys;
var dictionary = (_dictionary = {
  languageCode: 'zh-CN'
}, _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ROW_ABOVE, '上方插入行'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ROW_BELOW, '下方插入行'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_INSERT_LEFT, '左方插入列'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_INSERT_RIGHT, '右方插入列'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_REMOVE_ROW, ['移除该行', '移除多行']), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_REMOVE_COLUMN, ['移除该列', '移除多列']), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_UNDO, '撤销'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_REDO, '恢复'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_READ_ONLY, '只读'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_CLEAR_COLUMN, '清空该列'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT, '对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_LEFT, '左对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_CENTER, '水平居中'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_RIGHT, '右对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_JUSTIFY, '两端对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_TOP, '顶端对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_MIDDLE, '垂直居中'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ALIGNMENT_BOTTOM, '底端对齐'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_FREEZE_COLUMN, '冻结该列'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_UNFREEZE_COLUMN, '取消冻结'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_BORDERS, '边框'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_BORDERS_TOP, '上'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_BORDERS_RIGHT, '右'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_BORDERS_BOTTOM, '下'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_BORDERS_LEFT, '左'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_REMOVE_BORDERS, '移除边框'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_ADD_COMMENT, '插入批注'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_EDIT_COMMENT, '编辑批注'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_REMOVE_COMMENT, '删除批注'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_READ_ONLY_COMMENT, '只读批注'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_MERGE_CELLS, '合并'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_UNMERGE_CELLS, '取消合并'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_COPY, '复制'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_CUT, '剪切'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_NESTED_ROWS_INSERT_CHILD, '插入子行'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_NESTED_ROWS_DETACH_CHILD, '与母行分离'), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_HIDE_COLUMN, ['隐藏该列', '隐藏多列']), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_SHOW_COLUMN, ['显示该列', '显示多列']), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_HIDE_ROW, ['隐藏该行', '隐藏多行']), _defineProperty(_dictionary, C.CONTEXTMENU_ITEMS_SHOW_ROW, ['显示该行', '显示多行']), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_NONE, '无'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_EMPTY, '为空'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_NOT_EMPTY, '不为空'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_EQUAL, '等于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_NOT_EQUAL, '不等于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_BEGINS_WITH, '开头是'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_ENDS_WITH, '结尾是'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_CONTAINS, '包含'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_NOT_CONTAIN, '不包含'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_GREATER_THAN, '大于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_GREATER_THAN_OR_EQUAL, '大于或等于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_LESS_THAN, '小于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_LESS_THAN_OR_EQUAL, '小于或等于'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_BETWEEN, '在此范围'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_NOT_BETWEEN, '不在此范围'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_AFTER, '之后'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_BEFORE, '之前'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_TODAY, '今天'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_TOMORROW, '明天'), _defineProperty(_dictionary, C.FILTERS_CONDITIONS_YESTERDAY, '昨天'), _defineProperty(_dictionary, C.FILTERS_VALUES_BLANK_CELLS, '空白单元格'), _defineProperty(_dictionary, C.FILTERS_DIVS_FILTER_BY_CONDITION, '按条件过滤'), _defineProperty(_dictionary, C.FILTERS_DIVS_FILTER_BY_VALUE, '按值过滤'), _defineProperty(_dictionary, C.FILTERS_LABELS_CONJUNCTION, '且'), _defineProperty(_dictionary, C.FILTERS_LABELS_DISJUNCTION, '或'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_SELECT_ALL, '全选'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_CLEAR, '清除'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_OK, '确认'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_CANCEL, '取消'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_PLACEHOLDER_SEARCH, '搜索'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_PLACEHOLDER_VALUE, '值'), _defineProperty(_dictionary, C.FILTERS_BUTTONS_PLACEHOLDER_SECOND_VALUE, '第二值'), _dictionary);

_handsontable.default.languages.registerLanguageDictionary(dictionary);

var _default = dictionary;
exports.default = _default;

/***/ })

/******/ })["___"];
});