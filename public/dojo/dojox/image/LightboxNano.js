dojo.provide("dojox.image.LightboxNano");
dojo.require("dojo.fx");

(function(d){

var getViewport = function(){
		//	summary: Returns the dimensions and scroll position of the viewable area of a browser window
		var scrollRoot = (d.doc.compatMode == "BackCompat") ? d.body() : d.doc.documentElement,
			scroll = dojo._docScroll();
		return { w: scrollRoot.clientWidth, h: scrollRoot.clientHeight, l: scroll.x, t: scroll.y };
	},
	abs = "absolute";

d.declare("dojox.image.LightboxNano", null, {
	//	summary:
	//		A simple "nano" version of the lightbox. 
	//
	//	description:
	//		Very lightweight lightbox which only displays a larger image.  There is
	//		no support for a caption or description.  The lightbox can be closed by
	//		clicking any where or pressing any key.  This widget is intended to be
	//		used on <a> and <img> tags.  Upon creation, if the domNode is <img> tag,
	//		then it is wrapped in an <a> tag, then a <div class="enlarge"> is placed
	//		inside the <a> and can be styled to display an icon that the original
	//		can be enlarged.
	//
	//	example:
	//	|	<a dojoType="dojox.image.LightboxNano" href="/path/to/largeimage.jpg"><img src="/path/to/thumbnail.jpg"></a>
	//
	//	example:
	//	|	<img dojoType="dojox.image.LightboxNano" src="/path/to/thumbnail.jpg" href="/path/to/largeimage.jpg">

	//	href: string
	//		URL to the large image to show in the lightbox.
	href: "",

	//	duration: int
	//		The delay in milliseconds of the LightboxNano open and close animation.
	duration: 500,

	//	preloadDelay: int
	//		The delay in milliseconds after the LightboxNano is created before preloading the larger image.
	preloadDelay: 5000,

	constructor: function(p, n){
		// summary: Initializes the DOM node and connect onload event
		var _this = this;

		d.mixin(_this, p);
		n = dojo.byId(n);

		if(!/a/i.test(n.tagName)){
			var a = d.create("a", { href: _this.href, "class": n.className }, n, "after");
			n.className = "";
			a.appendChild(n);
			n = a;
		}

		d.style(n, {
			display: "block",
			position: "relative"
		});
		_this._createDiv("dojoxEnlarge", n);

		_this._node = n;
		d.setSelectable(n, false);
		_this._onClickEvt = d.connect(n, "onclick", _this, "_load");

		setTimeout(function(){
			(new Image()).src = _this.href;
			_this._hideLoading();
		}, _this.preloadDelay);
	},

	destroy: function(){
		// summary: Destroys the LightboxNano and it's DOM node
		var a = this._connects || [];
		a.push(this._onClickEvt);
		d.forEach(a, d.disconnect);
		d.destroy(this._node);
	},

	_createDiv: function(/*String*/cssClass, /*DomNode*/refNode, /*boolean*/display){
		// summary: Creates a div for the enlarge icon and loading indicator layers
		return d.create("div", { "class": cssClass, style: { position: abs, display: display ? "" : "none" } }, refNode); // DomNode
	},
	
	_load: function(/*Event*/e){
		// summary: Creates the large image and begins to show it
		var _this = this;

		d.stopEvent(e);

		if(!_this._loading){
			_this._loading = true;
			_this._reset();

			var n = d.query("img", _this._node)[0],
				a = d._abs(n, true),
				c = d.contentBox(n),
				b = d._getBorderExtents(n),
				i = _this._img = d.create("img", {
					style: {
						visibility: "hidden",
						cursor: "pointer",
						position: abs,
						top: 0,
						left: 0,
						zIndex: 9999999
					}
				}, d.body()),
				ln = _this._loadingNode;

			if(ln == null){
				_this._loadingNode = ln = _this._createDiv("dojoxLoading", _this._node, true)
				var l = d.marginBox(ln);
				d.style(ln, {
					left: parseInt((c.w - l.w) / 2) + "px",
					top: parseInt((c.h - l.h) / 2) + "px"
				});
			}

			c.x = a.x - 10 + b.l;
			c.y = a.y - 10 + b.t;
			_this._start = c;

			_this._connects = [d.connect(i, "onload", _this, "_show")];

			i.src = _this.href;
		}
	},

	_hideLoading: function(){
		// summary: Hides the animated loading indicator
		if(this._loadingNode){
			d.style(this._loadingNode, "display", "none");
		}
		this._loadingNode = false;
	},

	_show: function(){
		// summary: The image is now loaded, calculate size and display
		var _this = this,
			vp = getViewport(),
			w = _this._img.width,
			h = _this._img.height,
			vpw = parseInt((vp.w - 20) * 0.9),
			vph = parseInt((vp.h - 20) * 0.9),
			dd = d.doc,
			bg = _this._bg = d.create("div", {
				style: {
					backgroundColor: "#000",
					opacity: 0.0,
					position: abs,
					zIndex: 9999998
				}
			}, d.body()),
			ln = _this._loadingNode;

		if(_this._loadingNode){
			_this._hideLoading();
		}
		d.style(_this._img, {
			border: "10px solid #fff",
			visibility: "visible"
		});
		d.style(_this._node, "visibility", "hidden");

		_this._loading = false;

		_this._connects = _this._connects.concat([
			d.connect(dd, "onmousedown", _this, "_hide"),
			d.connect(dd, "onkeypress", _this, "_key"),
			d.connect(window, "onresize", _this, "_sizeBg")
		]);

		if(w > vpw){
			h = h * vpw / w;
			w = vpw;
		}
		if(h > vph){
			w = w * vph / h;
			h = vph;
		}

		_this._end = {
			x: (vp.w - 20 - w) / 2 + vp.l,
			y: (vp.h - 20 - h) / 2 + vp.t,
			w: w,
			h: h
		};

		_this._sizeBg();

		d.fx.combine([
			_this._anim(_this._img, _this._coords(_this._start, _this._end)),
			_this._anim(bg, { opacity: 0.5 })
		]).play();
	},

	_sizeBg: function(){
		// summary: Resize the background to fill the page
		var dd = d.doc.documentElement;
		d.style(this._bg, {
			top: 0,
			left: 0,
			width: dd.scrollWidth + "px",
			height: dd.scrollHeight + "px"
		});
	},

	_key: function(/*Event*/e){
		// summary: A key was pressed, so hide the lightbox
		d.stopEvent(e);
		this._hide();
	},

	_coords: function(/*Object*/s, /*Object*/e){
		// summary: Returns animation parameters with the start and end coords
		return {
			left:	{ start: s.x, end: e.x },
			top:	{ start: s.y, end: e.y },
			width:	{ start: s.w, end: e.w },
			height:	{ start: s.h, end: e.h }
		}; // object
	},

	_hide: function(){
		// summary: Closes the lightbox
		var _this = this;
		d.forEach(_this._connects, d.disconnect);
		_this._connects = [];
		d.fx.combine([
			_this._anim(_this._img, _this._coords(_this._end, _this._start), "_reset"),
			_this._anim(_this._bg, {opacity:0})
		]).play();
	},

	_reset: function(){
		// summary: Destroys the lightbox
		d.style(this._node, "visibility", "visible");
		d.forEach([this._img, this._bg], function(n){
			d.destroy(n);
			n = null;
		});
		this._node.focus();
	},

	_anim: function(node, args, onEnd){
		// summary: Creates the lightbox open/close and background fadein/out animations
		return d.animateProperty({
			node: node,
			duration: this.duration,
			properties: args,
			onEnd: onEnd ? d.hitch(this, onEnd) : null
		}); // object
	}
});

})(dojo);
