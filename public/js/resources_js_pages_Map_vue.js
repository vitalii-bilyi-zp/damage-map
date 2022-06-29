"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_pages_Map_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'Map',
  data: function data() {
    return {
      isLoading: false,
      dataLoaded: false,
      regionsData: [],
      districtsData: [],
      communitiesData: []
    };
  },
  mounted: function mounted() {
    this.loadMapData();
  },
  methods: {
    loadMapData: function loadMapData() {
      var _this = this;

      var promises = [this.$store.dispatch('loadRegionsData'), this.$store.dispatch('loadDistrictsData'), this.$store.dispatch('loadCommunitiesData')];
      this.isLoading = true;
      Promise.all(promises).then(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 3),
            regionsResponse = _ref2[0],
            districtsResponse = _ref2[1],
            communitiesResponse = _ref2[2];

        _this.regionsData = regionsResponse.data || [];
        _this.districtsData = districtsResponse.data || [];
        _this.communitiesData = communitiesResponse.data || [];

        if (!_this.dataLoaded) {
          _this.dataLoaded = true;

          _this.initMap();
        }
      })["catch"](function () {//
      })["finally"](function () {
        _this.isLoading = false;
      });
    },
    initMap: function initMap() {
      mapboxgl.accessToken = "pk.eyJ1IjoiYmVseXktdnYiLCJhIjoiY2wyeGM1YTJ6MDBiNjNibHBwdTZ5cG9uYiJ9.cYoj0o4mWMYonTsAzQO2eg"; // var bounds = [
      //     [20.435, 42.779],
      //     [42.935, 53.278]
      // ];

      var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/light-v10',
        center: [33.124, 48.742],
        //maxBounds: bounds,
        minZoom: 1,
        zoom: 5.5
      });
      var zoomStep0 = 5.5;
      var zoomStep1 = 6;
      var zoomStep2 = 7;
      var zoomStep3 = 8;
      var urlregion = 'geojson/region-ukraine-s-v5.geojson';
      var urlrayon = 'geojson/rayon-ukraine-s-v5.geojson';
      var titlerayon = 'geojson/rayon-title.geojson';
      var urlhromada = 'geojson/hromady-ukraine-s-v6.geojson';
      var titlehromada = 'geojson/all-hromady-titles-v3.geojson';
      var hoveredStateId = null;
      map.on('load', function () {
        var _map$addLayer;

        var layers = map.getStyle().layers; // Find the index of the first symbol layer in the map style

        var firstSymbolId;

        for (var i = 0; i < layers.length; i++) {
          if (layers[i].type === 'symbol') {
            firstSymbolId = layers[i].id;
            break;
          }
        }

        map.addControl(new mapboxgl.NavigationControl(), 'top-left');
        map.addControl(new mapboxgl.FullscreenControl(), 'top-left');
        map.addSource('region', {
          'type': 'geojson',
          'data': urlregion
        });
        map.addSource('titlerayon', {
          'type': 'geojson',
          'data': titlerayon
        });
        map.addSource('rayon', {
          'type': 'geojson',
          'data': urlrayon
        });
        map.addSource('titlehromada', {
          'type': 'geojson',
          'data': titlehromada
        });
        map.addSource('hromada', {
          'type': 'geojson',
          'data': urlhromada
        });
        map.addLayer({
          'id': 'region',
          'type': 'fill',
          'source': 'region',
          'maxzoom': zoomStep1,
          'layout': {},
          'paint': {
            'fill-color': ['interpolate', ['linear'], ['get', 'kv'], 0, '#F2F12D', 245000, '#EED322', 490000, '#E6B71E', 735000, '#DA9C20', 980000, '#CA8323', 1225000, '#B86B25', 1470000, '#A25626', 1710000, '#8B4225', 2200000, '#723122'],
            'fill-opacity': ['case', ['boolean', ['feature-state', 'hover'], false], 0.65, 0.5],
            'fill-outline-color': '#3A3A33'
          }
        }, firstSymbolId);
        map.addLayer({
          'id': 'region-border',
          'type': 'line',
          'source': 'region',
          'minzoom': zoomStep1,
          'maxzoom': zoomStep2,
          'layout': {},
          'paint': {
            'line-color': '#3A3A33',
            'line-width': 1
          }
        }, firstSymbolId);
        map.addLayer({
          'id': 'title-rayon',
          'type': 'symbol',
          'source': 'titlerayon',
          'minzoom': zoomStep1,
          'maxzoom': zoomStep2,
          'layout': {
            'text-field': ['get', 'titlerayon'],
            'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
            'text-size': 10,
            'text-transform': 'uppercase',
            'text-offset': [0, 1.25],
            'text-anchor': 'top'
          },
          'paint': {
            'text-color': '#57574D',
            'text-opacity': 0.75
          }
        });
        map.addLayer({
          'id': 'rayon',
          'type': 'fill',
          'source': 'rayon',
          'minzoom': zoomStep1,
          'maxzoom': zoomStep2,
          'layout': {},
          'paint': {
            'fill-color': ['interpolate', ['linear'], ['get', 'kv'], 0, '#FCFCFB', 19800, '#F9F9F7', 96000, '#F0F0EA', 120000, '#E7E7DD', 147000, '#D4D4C4', 170000, '#C2C2AB', 220000, '#AFAF9A', 360000, '#747467', 1200000, '#57574D'],
            'fill-opacity': ['case', ['boolean', ['feature-state', 'hover'], false], 0.65, 0.5],
            'fill-outline-color': '#3A3A33'
          }
        }, firstSymbolId);
        map.addLayer({
          'id': 'rayon-border',
          'type': 'line',
          'source': 'rayon',
          'minzoom': zoomStep2,
          'layout': {},
          'paint': {
            'line-color': '#3A3A33',
            'line-width': 1
          }
        }, firstSymbolId);
        map.addLayer({
          'id': 'title-hromada',
          'type': 'symbol',
          'source': 'titlehromada',
          'minzoom': zoomStep2,
          'layout': {
            'text-field': ['get', 'hromada'],
            'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
            'text-size': 9,
            'text-transform': 'uppercase',
            'text-offset': [0, 1.25],
            'text-anchor': 'top'
          },
          'paint': {
            'text-color': '#57574D',
            'text-opacity': 0.9
          }
        });
        map.addLayer((_map$addLayer = {
          'id': 'hromada',
          'type': 'fill',
          'source': 'hromada',
          'minzoom': zoomStep2,
          'layout': {}
        }, _defineProperty(_map$addLayer, "layout", {}), _defineProperty(_map$addLayer, 'paint', {
          'fill-color': ['interpolate', ['linear'], ['get', 'voters'], 0, '#fdfdb0', 5000, '#EED322', 10000, '#E6B71E', 20000, '#DA9C20', 50000, '#CA8323', 100000, '#B86B25', 500000, '#A25626', 1000000, '#8B4225', 2500000, '#723122'],
          'fill-opacity': ['case', ['boolean', ['feature-state', 'hover'], false], 0.65, 0.5],
          'fill-outline-color': '#723122'
        }), _map$addLayer), firstSymbolId);
        map.on('click', 'region', function (e) {
          var properties = e.features[0].properties;
          var html = "\n                        <h4 class=\"region\">".concat(properties.region, "</h4>\n                        <div>\n                            <table>\n                                <tbody>\n                                    <tr>\n                                        <td>\u0412\u0430\u0440\u0442\u0456\u0441\u0442\u044C \u0432\u0456\u0434\u043D\u043E\u0432\u043B\u0435\u043D\u043D\u044F</td>\n                                        <td><span>", 0, "</span> \u0433\u0440\u043D.</td>\n                                    </tr>\n                                </tbody>\n                            </table>\n                        </div>\n                    ");
          new mapboxgl.Popup().setLngLat(e.lngLat).setHTML(html).addTo(map);
        });
        map.on('click', 'rayon', function (e) {
          var properties = e.features[0].properties;
          var html = "\n                        <h4 class=\"rayon\">".concat(properties.rayon, "</h4>\n                        <div>\n                            <table>\n                                <tbody>\n                                    <tr>\n                                        <td>\u0412\u0430\u0440\u0442\u0456\u0441\u0442\u044C \u0432\u0456\u0434\u043D\u043E\u0432\u043B\u0435\u043D\u043D\u044F</td>\n                                        <td><span>", 0, "</span> \u0433\u0440\u043D.</td>\n                                    </tr>\n                                </tbody>\n                            </table>\n                        </div>\n                    ");
          new mapboxgl.Popup().setLngLat(e.lngLat).setHTML(html).addTo(map);
        });
        map.on('click', 'hromada', function (e) {
          var properties = e.features[0].properties;
          var html = "\n                        <h4 class=\"region\">".concat(properties.hromada, "</h4>\n                        <div>\n                            <p>\n                                <span>").concat(properties.region, "</span><br>\n                                ").concat(properties.rayon, "\n                            </p>\n                        </div>\n                        <div>\n                            <table>\n                                <tbody>\n                                    <tr>\n                                        <td>\u0412\u0430\u0440\u0442\u0456\u0441\u0442\u044C \u0432\u0456\u0434\u043D\u043E\u0432\u043B\u0435\u043D\u043D\u044F</td>\n                                        <td><span>", 0, "</span> \u0433\u0440\u043D.</td>\n                                    </tr>\n                                </tbody>\n                            </table>\n                        </div>\n                    ");
          new mapboxgl.Popup().setLngLat(e.lngLat).setHTML(html).addTo(map);
        });
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, ".map[data-v-405bff63] {\n  position: relative;\n  height: 100%;\n}\n.map__loader[data-v-405bff63] {\n  position: absolute;\n  top: 0;\n  left: 0;\n  width: 100%;\n  height: 100%;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  z-index: 1;\n}\n#map[data-v-405bff63] {\n  position: absolute;\n  top: 0;\n  left: 0;\n  width: 100%;\n  height: 100%;\n}\n[data-v-405bff63] .mapboxgl-popup-content div {\n  padding: 10px;\n}\n[data-v-405bff63] .mapboxgl-popup-content .region {\n  background-color: #f4e470;\n}\n[data-v-405bff63] .mapboxgl-popup-content .rayon {\n  background-color: #C2C2AB;\n}\n[data-v-405bff63] .mapboxgl-popup-content h4 {\n  text-transform: uppercase;\n  padding: 10px;\n  margin-top: 0px;\n}\n[data-v-405bff63] .mapboxgl-popup-content ul {\n  padding-left: 10px;\n  list-style: none;\n}\n[data-v-405bff63] .mapboxgl-popup-content table {\n  width: 100%;\n  box-sizing: border-box;\n}\n[data-v-405bff63] .mapboxgl-popup-content table td {\n  border: 1px solid #dbdbdb;\n  border-width: 0 0 1px;\n}\n[data-v-405bff63] .mapboxgl-popup-content thead tr th:first-child,[data-v-405bff63] tbody tr td:first-child {\n  width: 150px;\n  min-width: 150px;\n  max-width: 150px;\n}\n[data-v-405bff63] .mapboxgl-popup-content p {\n  margin-bottom: 0;\n}\n[data-v-405bff63] .mapboxgl-popup-content span {\n  text-transform: uppercase;\n}\n[data-v-405bff63] .mapboxgl-popup {\n  max-width: 400px;\n  font-size: 12px;\n}\n[data-v-405bff63] .mapboxgl-popup-content {\n  width: 300px;\n  padding: 15px;\n}\n[data-v-405bff63] .mapboxgl-popup-close-button {\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  width: 16px;\n  height: 16px;\n  font-size: 16px;\n}", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_css_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_2_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_style_index_0_id_405bff63_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& */ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_css_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_2_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_style_index_0_id_405bff63_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_css_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_2_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_style_index_0_id_405bff63_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./resources/js/pages/Map.vue":
/*!************************************!*\
  !*** ./resources/js/pages/Map.vue ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Map.vue?vue&type=template&id=405bff63&scoped=true& */ "./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true&");
/* harmony import */ var _Map_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Map.vue?vue&type=script&lang=js& */ "./resources/js/pages/Map.vue?vue&type=script&lang=js&");
/* harmony import */ var _Map_vue_vue_type_style_index_0_id_405bff63_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& */ "./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");



;


/* normalize component */

var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Map_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,
  _Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  "405bff63",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Map.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Map.vue?vue&type=script&lang=js&":
/*!*************************************************************!*\
  !*** ./resources/js/pages/Map.vue?vue&type=script&lang=js& ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Map.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_css_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_2_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_12_0_rules_0_use_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_style_index_0_id_405bff63_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader/dist/cjs.js!../../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true& */ "./node_modules/style-loader/dist/cjs.js!./node_modules/css-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[2]!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-12[0].rules[0].use[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=style&index=0&id=405bff63&lang=scss&scoped=true&");


/***/ }),

/***/ "./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true&":
/*!*******************************************************************************!*\
  !*** ./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true& ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Map_vue_vue_type_template_id_405bff63_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Map.vue?vue&type=template&id=405bff63&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/pages/Map.vue?vue&type=template&id=405bff63&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "map" }, [
    _vm.isLoading
      ? _c(
          "div",
          { staticClass: "map__loader" },
          [
            _c("v-progress-circular", {
              attrs: {
                color: "#757575",
                size: 60,
                width: 3,
                indeterminate: "",
              },
            }),
          ],
          1
        )
      : _vm._e(),
    _vm._v(" "),
    _c("div", { attrs: { id: "map" } }),
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);