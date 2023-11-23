(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

/* jshint esnext:true */

(function ($, undefined) {
  'use strict';

  var api = wp.customize;

  // toggle visibility of some controls based on a setting's value
  // @see wp-admin/js/customize-controls.js
  $.each({
    'vamtam_theme[header-logo-type]': [{
      controls: ['vamtam_theme[custom-header-logo]'],
      callback: function callback(to) {
        return 'image' === to;
      }
    }],
    'vamtam_theme[site-layout-type]': [{
      controls: ['vamtam_theme[full-width-header]'],
      callback: function callback(to) {
        return 'boxed' !== to;
      }
    }, {
      controls: ['vamtam_theme[boxed-layout-padding]'],
      callback: function callback(to) {
        return 'boxed' === to;
      }
    }, {
      controls: ['vamtam_theme[body-background]'],
      callback: function callback(to) {
        return 'full' !== to;
      }
    }],
    'vamtam_theme[header-layout]': [{
      controls: ['vamtam_theme[full-width-header]'],
      callback: function callback(to) {
        return 'logo-menu' === to;
      } // show if header is 'logo-menu'
    }, {
      controls: ['vamtam_theme[sub-header-background]'],
      callback: function callback(to) {
        return 'logo-menu' !== to;
      } // show if header is not 'logo-menu'
    }],

    'vamtam_theme[top-bar-layout]': [{
      controls: ['vamtam_theme[top-bar-social-lead]', 'vamtam_theme[top-bar-social-fb]', 'vamtam_theme[top-bar-social-twitter]', 'vamtam_theme[top-bar-social-linkedin]', 'vamtam_theme[top-bar-social-gplus]', 'vamtam_theme[top-bar-social-flickr]', 'vamtam_theme[top-bar-social-pinterest]', 'vamtam_theme[top-bar-social-dribbble]', 'vamtam_theme[top-bar-social-instagram]', 'vamtam_theme[top-bar-social-youtube]', 'vamtam_theme[top-bar-social-vimeo]'],
      callback: function callback(to) {
        return ['menu-social', 'social-menu', 'social-text', 'text-social'].indexOf(to) > -1;
      }
    }, {
      controls: ['vamtam_theme[top-bar-text]'],
      callback: function callback(to) {
        return ['menu-text', 'text-menu', 'social-text', 'text-social', 'fulltext'].indexOf(to) > -1;
      }
    }]
  }, function (settingId, conditions) {
    api(settingId, function (setting) {
      $.each(conditions, function (cndi, o) {
        $.each(o.controls, function (i, controlId) {
          api.control(controlId, function (control) {
            var visibility = function visibility(to) {
              control.container.toggle(o.callback(to));
            };
            visibility(setting.get());
            setting.bind(visibility);
          });
        });
      });
    });
  });
})(jQuery);

},{}]},{},[1]);
