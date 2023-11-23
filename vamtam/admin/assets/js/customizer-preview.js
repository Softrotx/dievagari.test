(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _helpers = require("./helpers");
/* jshint esnext:true */

var general = function general(api, $) {
  'use strict';

  api('vamtam_theme[show-splash-screen]', function (value) {
    value.bind(function (to) {
      if (+to) {
        $('body').triggerHandler('vamtam-preview-splash-screen');
      }
    });
  });
  api('vamtam_theme[splash-screen-logo]', function (value) {
    value.bind(function (to) {
      var wrapper = $('.vamtam-splash-screen-progress-wrapper');
      var current_image = wrapper.find('> img');
      if (current_image.length === 0) {
        current_image = $('<img />');
        wrapper.prepend(current_image);
      }
      current_image.attr('src', to);
      $('body').triggerHandler('vamtam-preview-splash-screen');
    });
  });
  api('vamtam_theme[show-scroll-to-top]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('#scroll-to-top'), to);
    });
  });
  api('vamtam_theme[show-related-posts]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('.vamtam-related-content.related-posts'), to);
    });
  });
  api('vamtam_theme[related-posts-title]', function (value) {
    value.bind(function (to) {
      $('.related-posts .related-content-title').html(to);
    });
  });
  api('vamtam_theme[show-single-post-image]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('.single-post > .post-media-image'), to);
    });
  });
  api('vamtam_theme[post-meta]', function (value) {
    value.bind(function (to) {
      for (var type in to) {
        (0, _helpers.toggle)($('.vamtam-meta-' + type), +to[type]);
      }
    });
  });
  api('vamtam_theme[show-related-portfolios]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('.vamtam-related-content.related-portfolios'), to);
    });
  });
  api('vamtam_theme[related-portfolios-title]', function (value) {
    value.bind(function (to) {
      $('.related-portfolios .related-content-title').html(to);
    });
  });
};
var _default = general;
exports["default"] = _default;

},{"./helpers":2}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.hexToHsl = hexToHsl;
exports.hexToRgb = hexToRgb;
exports.isNumeric = isNumeric;
exports.rgbToHsl = rgbToHsl;
exports.toggle = void 0;
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
/* jshint esnext:true */

var toggle = function toggle(el, visibility) {
  'use strict';

  if (+visibility) {
    el.show();
  } else {
    el.hide();
  }
};
exports.toggle = toggle;
function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

/**
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes r, g, and b are contained in the set [0, 255] and
 * returns h, s, and l for use with the hsl() notation in CSS
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
 */
function rgbToHsl(r, g, b) {
  r /= 255, g /= 255, b /= 255;
  var max = Math.max(r, g, b),
    min = Math.min(r, g, b);
  var h,
    s,
    l = (max + min) / 2;
  if (max == min) {
    h = s = 0; // achromatic
  } else {
    var d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case r:
        h = (g - b) / d + (g < b ? 6 : 0);
        break;
      case g:
        h = (b - r) / d + 2;
        break;
      case b:
        h = (r - g) / d + 4;
        break;
    }
    h /= 6;
  }
  return [h * 360, s * 100, l * 100];
}
function hexToRgb(hex) {
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function (m, r, g, b) {
    return r + r + g + g + b + b;
  });
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)] : null;
}
function hexToHsl(hex) {
  return rgbToHsl.apply(void 0, _toConsumableArray(hexToRgb(hex)));
}

},{}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _helpers = require("./helpers");
/* jshint esnext:true */

var layout = function layout(api, $) {
  'use strict';

  api('vamtam_theme[full-width-header]', function (value) {
    value.bind(function (to) {
      $('.header-maybe-limit-wrapper').toggleClass('limit-wrapper', to);
    });
  });
  api('vamtam_theme[sticky-header]', function (value) {
    value.bind(function (to) {
      requestAnimationFrame(function () {
        document.body.classList.toggle('sticky-header', +to);
        document.body.classList.remove('had-sticky-header');
        window.VAMTAM.stickyHeader.rebuild();
      });
    });
  });
  api('vamtam_theme[enable-header-search]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('header.main-header .search-wrapper'), +to);
    });
  });
  api('vamtam_theme[show-empty-header-cart]', function (value) {
    value.bind(function (to) {
      document.querySelectorAll('.vamtam-header-cart-wrapper').forEach(function (el) {
        return el.classList.toggle('show-if-empty', +to);
      });
      $('body').trigger('wc_fragments_refreshed');
    });
  });
  api('vamtam_theme[one-page-footer]', function (value) {
    value.bind(function (to) {
      (0, _helpers.toggle)($('.footer-wrapper'), to);
      setTimeout(function () {
        window.VAMTAM.resizeElements();
      }, 50);
    });
  });
  api('vamtam_theme[page-title-layout]', function (value) {
    value.bind(function (to) {
      var header = $('header.page-header');
      var line = header.find('.page-header-line');
      header.removeClass('layout-centered layout-one-row-left layout-one-row-right layout-left-align layout-right-align').addClass('layout-' + to);
      if (to.match(/one-row-/)) {
        line.appendTo(header.find('h1'));
      } else {
        line.appendTo(header);
      }
    });
  });
};
var _default = layout;
exports["default"] = _default;

},{"./helpers":2}],4:[function(require,module,exports){
"use strict";

var _general = _interopRequireDefault(require("./general"));
var _layout = _interopRequireDefault(require("./layout"));
var _styles = _interopRequireDefault(require("./styles"));
function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }
/* jshint esnext:true */

(function ($, undefined) {
  'use strict';

  (0, _general["default"])(wp.customize, $);
  (0, _layout["default"])(wp.customize, $);
  (0, _styles["default"])(wp.customize, $);
})(jQuery);

},{"./general":1,"./layout":3,"./styles":5}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _helpers = require("./helpers");
/* jshint esnext:true */

var styles = function styles(api, $) {
  'use strict';

  var prepare_background = function prepare_background(to) {
    if (to['background-image'] !== '') {
      to['background-image'] = 'url(' + to['background-image'] + ')';
    }
    return to;
  };
  {
    var compiler_options = VAMTAM_CUSTOMIZE_PREVIEW.compiler_options;
    var real_id = function real_id(id) {
      return id.replace(/vamtam_theme\[([^\]]+)]/, '$1');
    };
    var change_handler_by_type = {
      number: function number(to) {
        var id = real_id(this.id);
        if (VAMTAM_CUSTOMIZE_PREVIEW.percentages.indexOf(id) !== -1) {
          to += '%';
        } else if (VAMTAM_CUSTOMIZE_PREVIEW.numbers.indexOf(id) !== -1) {
          // as is
        } else {
          to += 'px';
        }
        document.documentElement.style.setProperty("--vamtam-".concat(id), to);

        // trigger a resize event if we change any dimension
        $(window).resize();
      },
      background: function background(to) {
        var id = real_id(this.id);
        to = prepare_background(to);
        for (var prop in to) {
          document.documentElement.style.setProperty("--vamtam-".concat(id, "-").concat(prop), to[prop]);
        }
      },
      radio: function radio(to) {
        var id = real_id(this.id);
        if ((0, _helpers.isNumeric)(to)) {
          change_handler_by_type.number.call(this, to);
        } else {
          document.documentElement.style.setProperty("--vamtam-".concat(id), to);
        }
      },
      select: function select(to) {
        var id = real_id(this.id);
        change_handler_by_type.radio.call(this, to);
      },
      typography: function typography(to, from) {
        var id = real_id(this.id);
        var variant = to.variant;
        to['font-weight'] = 'normal';
        to['font-style'] = 'normal';
        to.variant = to.variant.split(' ');
        if (to.variant.length === 2) {
          to['font-weight'] = to.variant[0];
          to['font-style'] = to.variant[1];
        } else if (to.variant[0] === 'italic') {
          to['font-style'] = 'italic';
        } else {
          to['font-weight'] = to.variant[0];
        }
        delete to.variant;
        for (var prop in to) {
          document.documentElement.style.setProperty("--vamtam-".concat(id, "-").concat(prop), to[prop]);
        }

        // if the font-family is changed - we need to load the new font stylesheet
        if (to['font-family'] !== from['font-family'] || to['variant'] !== from['variant']) {
          var new_font = window.top.VAMTAM_ALL_FONTS[to['font-family']];
          if (new_font.gf) {
            var family = encodeURIComponent(to['font-family']) + ':' + new_font.weights.join(',').replace(' ', '');
            var subset = ''; // no subset support here, only newer browser can preview Google Fonts

            var link = document.createElement("link");
            link.href = 'https://fonts.googleapis.com/css?family=' + family + '&subset=' + subset;
            link.type = 'text/css';
            link.rel = 'stylesheet';
            document.getElementsByTagName('head')[0].appendChild(link);
          }
        }
      },
      'color-row': function colorRow(to) {
        var id = real_id(this.id);
        for (var prop in to) {
          document.documentElement.style.setProperty("--vamtam-".concat(id, "-").concat(prop), to[prop]);
        }
        if (id === 'accent-color') {
          // accents need readable colors
          for (var i = 1; i <= 8; i++) {
            var hex = to[i];
            var hsl = (0, _helpers.hexToHsl)(hex);
            var readable = '';
            var hc = '';
            if (hsl[2] > 80) {
              readable = "hsl(".concat(hsl[0], ", ").concat(hsl[1], "%, ").concat(Math.max(0, hsl[2] - 50), "%)"); //  $color->darken( 50 );
              hc = '#000000';
            } else {
              readable = "hsl(".concat(hsl[0], ", ").concat(hsl[1], "%, ").concat(Math.min(0, hsl[2] + 50), "%)"); //  $color->lighten( 50 );
              hc = '#ffffff';
            }
            document.documentElement.style.setProperty("--vamtam-accent-color-".concat(i, "-readable"), readable);
            document.documentElement.style.setProperty("--vamtam-accent-color-".concat(i, "-hc"), hc);
            document.documentElement.style.setProperty("--vamtam-accent-color-".concat(i, "-transparent"), "hsl(".concat(hsl[0], ", ").concat(hsl[1], "%, ").concat(hsl[2], "%, 0)"));
          }
        }
      },
      color: function color(to) {
        var id = real_id(this.id);
        document.documentElement.style.setProperty("--vamtam-".concat(id), to);
      }
    };

    // const compiler_option_handler = ;
    var _loop = function _loop(opt_name) {
      api(opt_name, function (setting) {
        var type = compiler_options[opt_name];
        if (type in change_handler_by_type) {
          setting.bind(change_handler_by_type[type]);
        } else {
          console.error("VamTam Customzier: Missing handler for option type ".concat(type, " - option ").concat(opt_name));
          window.wpvval = setting;
        }
      });
    };
    for (var opt_name in compiler_options) {
      _loop(opt_name);
    }
  }
  api('vamtam_theme[page-title-background-hide-lowres]', function (value) {
    value.bind(function (to) {
      $('header.page-header').toggleClass('vamtam-hide-bg-lowres', to);
    });
  });
  api('vamtam_theme[main-background-hide-lowres]', function (value) {
    value.bind(function (to) {
      $('.vamtam-main').toggleClass('vamtam-hide-bg-lowres', to);
    });
  });
};
var _default = styles;
exports["default"] = _default;

},{"./helpers":2}]},{},[4]);
