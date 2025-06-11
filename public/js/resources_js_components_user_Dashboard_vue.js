"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_user_Dashboard_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_0__);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data: function data() {
    return {
      jumlahPengajuan: 0,
      pengajuanProses: 0,
      pengajuanSelesai: 0,
      jumlahPengguna: 0
    };
  },
  methods: {
    // async fetchPengajuan() {
    //   try {
    //     const response = await axios.get("https://surat-desa.surabayawebtech.com/api/auth/pengajuan",{
    //       headers: {
    //         Authorization: 'Bearer ' + localStorage.getItem('token')
    //       }
    //     });
    //    this.jumlahPengajuan = response.data.data.length; // jumlah pengajuan
    //     this.pengajuanProses = response.data.data.filter(item => item.status_surat === 'proses').length; // jumlah status proses
    //     this.pengajuanSelesai = response.data.data.filter(item => item.status_surat === 'selesai').length; // jumlah status selesai

    //    } catch (error) {
    //     console.error(error);
    //   }
    // },
    // async fetchUser() {
    //   try {
    //     const response = await axios.get("https://surat-desa.surabayawebtech.com/api/auth/list-user",{
    //       headers: {
    //         Authorization: 'Bearer ' + localStorage.getItem('token')
    //       }
    //     });
    //     this.jumlahPengguna = response.data.data.length; //jumlah pengguna
    //   } catch (error) {
    //     console.error(error);
    //   }
    // },
  },
  created: function created() {
    // axios
    //   .get(`https://surat-desa.surabayawebtech.com/api/auth/me`, {
    //     headers: {
    //       Authorization: "Bearer " + localStorage.getItem("token"),
    //     },
    //   })
    //   .then((response) => {
    //     const role = response.data.role; // Get the user's role from the response

    //     const token = localStorage.getItem("token");
    //     const expires_in = localStorage.getItem("expires_in");
    //     if (!token || !expires_in || new Date() > new Date(expires_in)) {
    //       // If token is missing or expired, redirect to the home page
    //       localStorage.removeItem("token");
    //       localStorage.removeItem("expires_in");
    //       this.$router.push("/");
    //     } else if (role !== "user") {
    //       // If the user doesn't have admin privileges, redirect to the unauthorized page
    //       this.$router.push("/unauthorized");
    //       // console.log(response.data.role)
    //     } else {
    //       this.fetchPengajuan();
    //       this.fetchUser();
    //     }
    //   })
    //   .catch((error) => {
    //     console.error(error);
    //     this.$router.push("/");
    //   });
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render),
/* harmony export */   staticRenderFns: () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    attrs: {
      id: "wrapper"
    }
  }, [_c("sidebar"), _vm._v(" "), _c("div", {
    staticClass: "d-flex flex-column",
    attrs: {
      id: "content-wrapper"
    }
  }, [_c("div", {
    attrs: {
      id: "content"
    }
  }, [_c("navbar"), _vm._v(" "), _c("div", {
    staticClass: "container-fluid"
  }, [_vm._m(0), _vm._v(" "), _c("div", {
    staticClass: "row"
  }, [_c("div", {
    staticClass: "col-xl-3 col-md-6 mb-4"
  }, [_c("div", {
    staticClass: "card border-left-primary shadow h-100 py-2"
  }, [_c("div", {
    staticClass: "card-body"
  }, [_c("div", {
    staticClass: "row no-gutters align-items-center"
  }, [_c("div", {
    staticClass: "col mr-2"
  }, [_c("div", {
    staticClass: "text-xs font-weight-bold text-primary text-uppercase mb-1"
  }, [_vm._v("\n                      Jumlah Pengajuan\n                    ")]), _vm._v(" "), _c("div", {
    staticClass: "h5 mb-0 font-weight-bold text-gray-800"
  }, [_vm._v("\n                      " + _vm._s(_vm.jumlahPengajuan) + "\n                    ")])]), _vm._v(" "), _vm._m(1)])])])]), _vm._v(" "), _c("div", {
    staticClass: "col-xl-3 col-md-6 mb-4"
  }, [_c("div", {
    staticClass: "card border-left-success shadow h-100 py-2"
  }, [_c("div", {
    staticClass: "card-body"
  }, [_c("div", {
    staticClass: "row no-gutters align-items-center"
  }, [_c("div", {
    staticClass: "col mr-2"
  }, [_c("div", {
    staticClass: "text-xs font-weight-bold text-success text-uppercase mb-1"
  }, [_vm._v("\n                      Pengajuan Selesai\n                    ")]), _vm._v(" "), _c("div", {
    staticClass: "h5 mb-0 font-weight-bold text-gray-800"
  }, [_vm._v("\n                      " + _vm._s(_vm.pengajuanSelesai) + "\n                    ")])]), _vm._v(" "), _vm._m(2)])])])]), _vm._v(" "), _c("div", {
    staticClass: "col-xl-3 col-md-6 mb-4"
  }, [_c("div", {
    staticClass: "card border-left-info shadow h-100 py-2"
  }, [_c("div", {
    staticClass: "card-body"
  }, [_c("div", {
    staticClass: "row no-gutters align-items-center"
  }, [_c("div", {
    staticClass: "col mr-2"
  }, [_c("div", {
    staticClass: "text-xs font-weight-bold text-info text-uppercase mb-1"
  }, [_vm._v("\n                      Pengajuan Proses \n                    ")]), _vm._v(" "), _c("div", {
    staticClass: "row no-gutters align-items-center"
  }, [_c("div", {
    staticClass: "col-auto"
  }, [_c("div", {
    staticClass: "h5 mb-0 mr-3 font-weight-bold text-gray-800"
  }, [_vm._v("\n                          " + _vm._s(_vm.pengajuanProses) + "\n                        ")])]), _vm._v(" "), _c("div", {
    staticClass: "col"
  })])]), _vm._v(" "), _vm._m(3)])])])]), _vm._v(" "), _c("div", {
    staticClass: "col-xl-3 col-md-6 mb-4"
  }, [_c("div", {
    staticClass: "card border-left-warning shadow h-100 py-2"
  }, [_c("div", {
    staticClass: "card-body"
  }, [_c("div", {
    staticClass: "row no-gutters align-items-center"
  }, [_c("div", {
    staticClass: "col mr-2"
  }, [_c("div", {
    staticClass: "text-xs font-weight-bold text-warning text-uppercase mb-1"
  }, [_vm._v("\n                      Jumlah Pengguna\n                    ")]), _vm._v(" "), _c("div", {
    staticClass: "h5 mb-0 font-weight-bold text-gray-800"
  }, [_vm._v("\n                      " + _vm._s(_vm.jumlahPengguna) + "\n                    ")])]), _vm._v(" "), _vm._m(4)])])])])])])], 1), _vm._v(" "), _c("footer")])], 1);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "d-sm-flex align-items-center justify-content-between mb-4"
  }, [_c("h1", {
    staticClass: "h3 mb-0 text-gray-800"
  }, [_vm._v("Dashboard")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-auto"
  }, [_c("i", {
    staticClass: "fas fa-solid fa-list fa-2x text-gray-300"
  })]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-auto"
  }, [_c("i", {
    staticClass: "fas fa-solid fa-check-double fa-2x text-gray-300"
  })]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-auto"
  }, [_c("i", {
    staticClass: "fas fa-clipboard-list fa-2x text-gray-300"
  })]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "col-auto"
  }, [_c("i", {
    staticClass: "fas fa-solid fa-users fa-2x text-gray-300"
  })]);
}];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/components/user/Dashboard.vue":
/*!****************************************************!*\
  !*** ./resources/js/components/user/Dashboard.vue ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=template&id=4bc4503b */ "./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b");
/* harmony import */ var _Dashboard_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=script&lang=js */ "./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Dashboard_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__.render,
  _Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/user/Dashboard.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js":
/*!****************************************************************************!*\
  !*** ./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Dashboard.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=script&lang=js");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b":
/*!**********************************************************************************!*\
  !*** ./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   staticRenderFns: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_4bc4503b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Dashboard.vue?vue&type=template&id=4bc4503b */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/user/Dashboard.vue?vue&type=template&id=4bc4503b");


/***/ })

}]);