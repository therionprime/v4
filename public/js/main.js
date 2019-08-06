(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/js/main"],{

/***/ "./resources/assets/js/autocomplete.js":
/*!*********************************************!*\
  !*** ./resources/assets/js/autocomplete.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function render(str) {
  var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var fn = new Function("obj", "\n          var p = [];\n          var print = function () {\n              p.push.apply(p, arguments);\n          };\n          with (obj) {\n              p.push('".concat(str.replace(/[\r\t\n]/g, " ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g, "$1\r").replace(/\t=(.*?)%>/g, "',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'"), "');\n          };\n          return p.join('');\n      "));
  return fn(data);
}

function getJSON(url, callback) {
  var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'json';
  var xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.responseType = type;

  xhr.onload = function () {
    var status = xhr.status;

    if (status === 200) {
      callback(null, xhr.response);
    } else {
      callback(status, xhr.response);
    }
  };

  xhr.send();
}

function autocomplete(id) {
  var cb = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var el = document.getElementById(id);
  var url = el.dataset.fetch;
  var result = el.dataset.result;
  var resultEl = document.getElementById(result);
  var template = el.dataset.template;
  getJSON(url, function (err, json) {
    if (err) return;
    getJSON(template, function (err, tmpl) {
      if (err) return;

      if (cb && Array.isArray(json)) {
        el.addEventListener("keyup", function (e) {
          if (!el.value || el.value.length < 2) return resultEl.innerHTML = "";
          var $json = json.slice();
          modified = cb.call(undefined, e, el, $json) || $json;
          var res = modified.map(function (item) {
            return render(tmpl, item);
          }).join("");
          resultEl.innerHTML = res;
        });
      }
    }, 'text');
  });
}

window.autocomplete = autocomplete;
window.getJSON = getJSON;
window.render = render;

/***/ }),

/***/ "./resources/assets/js/main.js":
/*!*************************************!*\
  !*** ./resources/assets/js/main.js ***!
  \*************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _autocomplete__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./autocomplete */ "./resources/assets/js/autocomplete.js");
/* harmony import */ var _autocomplete__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_autocomplete__WEBPACK_IMPORTED_MODULE_0__);


(function () {
  var nav = document.querySelector('nav.fixed');
  var dropnavs = document.querySelectorAll('.dropnav');
  var height = nav.getBoundingClientRect().height;
  var progress = document.getElementById('read-progress');

  var fn = function fn() {
    var height2 = nav.getBoundingClientRect().height;
    var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    var winHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var scrolled = winScroll / winHeight * 100;

    if (progress) {
      progress.style.width = scrolled + "%";
    } // console.log(height2, height)
    // if (height2 != height) {


    document.body.style['margin-top'] = height2 + 'px'; // }

    if (window.pageYOffset > height2) {
      nav.classList.add('bg-white', 'text-dark');
      nav.classList.remove('bg-blue', 'text-white');
      Array.from(dropnavs).forEach(function (dropnav) {
        dropnav.classList.remove('bg-white', 'text-dark');
        dropnav.classList.add('bg-blue', 'text-white');
      });
    } else {
      nav.classList.remove('bg-white', 'text-dark');
      nav.classList.add('bg-blue', 'text-white');
      Array.from(dropnavs).forEach(function (dropnav) {
        dropnav.classList.add('bg-white', 'text-dark');
        dropnav.classList.remove('bg-blue', 'text-white');
      });
    }

    height = height2;
  };

  fn();
  window.addEventListener('scroll', fn);
  window.addEventListener('resize', fn);
})();

(function () {
  if (!window.Waves) return;
  new Waves({
    canvas: 'waves',
    waveCount: 5,
    backgroundColor: '#3EB5F7',
    backgroundBlendMode: 'source-over',
    waveBlendMode: 'screen',
    scale: 0.5
  }, {
    color: 'yellow',
    amplitude: 30
  }, {
    color: 'cyan',
    amplitude: 20
  }, {
    color: '#3EB5F7',
    amplitude: 30
  });
  $('#waves').parent().css('margin-bottom', '6rem');
})();

window.loadMoreApps = function (el) {
  var meta = document.head.querySelector('meta[name=page][content]');
  var currentPage = parseInt(meta.content);
  var nextPage = currentPage + 1;

  if (window.location.href.includes('?')) {
    var url = window.location.href + "&json=true&page=" + nextPage;
  } else {
    var url = window.location.origin + "/apps?json=true&page=" + nextPage;
  }

  getJSON(url, function (err, json) {
    if (err) {
      el.innerHTML = "No more apps. Try again?";
      el.className = "btn btn-red";
    } else {
      getJSON(el.dataset.template, function (err, template) {
        if (err) {
          el.innerHTML = "Template Error. Try again?";
          el.className = "btn btn-red";
        }

        var apps = document.getElementById('apps');
        apps.innerHTML += json.apps.data.map(function (app) {
          return render(template, app);
        }).join("");
        meta.setAttribute('content', nextPage.toString());
      }, 'text');
    }
  });
};

(function () {// var i = setInterval(function () {
  //   console.clear()
  //   console.log("%cHello!", "color: #3EB5F7; text-shadow: 0px 2px black; -webkit-text-stroke: 1px black; font-size: 60px;font-weight:bold;");
  //   console.log("%cDo you want to help develop this website?", "font-size: 20px;")
  //   console.log("%cIf you do, then contact @wizardzeb on Twitter.", "font-size: 20px;")
  // }, 1000)
  // setTimeout(function () {
  //   clearInterval(i)
  // }, 10000)
})();

/***/ }),

/***/ 2:
/*!*******************************************!*\
  !*** multi ./resources/assets/js/main.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/zeb/Documents/v4/resources/assets/js/main.js */"./resources/assets/js/main.js");


/***/ })

},[[2,"/js/manifest"]]]);