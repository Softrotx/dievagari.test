(function() {
	'use strict';

	// ChildNode (MDN)

	var buildDOM = function() {
		var nodes = Array.prototype.slice.call(arguments),
			frag = document.createDocumentFragment(),
			div, node;

		while (node = nodes.shift()) {
			if (typeof node == "string") {
				div = document.createElement("div");
				div.innerHTML = node;
				while (div.firstChild) {
					frag.appendChild(div.firstChild);
				}
			} else {
				frag.appendChild(node);
			}
		}

		return frag;
	};

	var proto = {
		before: function() {
			var frag = buildDOM.apply(this, arguments);
			this.parentNode.insertBefore(frag, this);
		},
		after: function() {
			var frag = buildDOM.apply(this, arguments);
			this.parentNode.insertBefore(frag, this.nextSibling);
		},
		replaceWith: function() {
			if (this.parentNode) {
				var frag = buildDOM.apply(this, arguments);
				this.parentNode.replaceChild(frag, this);
			}
		},
		remove: function() {
			if (this.parentNode) {
				this.parentNode.removeChild(this);
			}
		}
	};

	var a = ["Element", "DocumentType", "CharacterData"]; // interface
	var b = ["before", "after", "replaceWith", "remove"]; // methods
	a.forEach(function(v) {
		b.forEach(function(func) {
			if (window[v]) {
				if (window[v].prototype[func]) { return; }
				window[v].prototype[func] = proto[func];
			}
		});
	});

	// ParentNode.prepend()
	// Source: https://github.com/jserz/js_piece/blob/master/DOM/ParentNode/prepend()/prepend().md

	(function(arr) {
		arr.forEach(function(item) {
			if (item.hasOwnProperty('prepend')) {
				return;
			}
			Object.defineProperty(item, 'prepend', {
				configurable: true,
				enumerable: true,
				writable: true,
				value: function prepend() {
					var argArr = Array.prototype.slice.call(arguments),
						docFrag = document.createDocumentFragment();

					argArr.forEach(function(argItem) {
						var isNode = argItem instanceof Node;
						docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)));
					});

					this.insertBefore(docFrag, this.firstChild);
				}
			});
		});
	})([Element.prototype, Document.prototype, DocumentFragment.prototype]);

	// Object.assign() (MDN)

	if (typeof Object.assign != 'function') {
	  (function () {
		Object.assign = function (target) {
		  // We must check against these specific cases.
		  if (target === undefined || target === null) {
			throw new TypeError('Cannot convert undefined or null to object');
		  }

		  var output = Object(target);
		  for (var index = 1; index < arguments.length; index++) {
			var source = arguments[index];
			if (source !== undefined && source !== null) {
			  for (var nextKey in source) {
				if (source.hasOwnProperty(nextKey)) {
				  output[nextKey] = source[nextKey];
				}
			  }
			}
		  }
		  return output;
		};
	  })();
	}

	// Element.prototype.matches (https://plainjs.com/javascript/traversing/get-closest-element-by-selector-39/)
	window.Element && function(ElementPrototype) {
		ElementPrototype.matches = ElementPrototype.matches ||
		ElementPrototype.matchesSelector ||
		ElementPrototype.webkitMatchesSelector ||
		ElementPrototype.msMatchesSelector ||
		function(selector) {
			var node = this, nodes = (node.parentNode || node.document).querySelectorAll(selector), i = -1;
			while (nodes[++i] && nodes[i] != node);
			return !!nodes[i];
		};
	}(Element.prototype);

	// Element.prototype.closest (https://plainjs.com/javascript/traversing/get-closest-element-by-selector-39/)
	window.Element && function(ElementPrototype) {
		ElementPrototype.closest = ElementPrototype.closest ||
		function(selector) {
			var el = this;
			while (el.matches && !el.matches(selector)) el = el.parentNode;
			return el.matches ? el : null;
		};
	}(Element.prototype);
}());
