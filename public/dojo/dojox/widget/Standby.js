dojo.provide("dojox.widget.Standby");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.fx");

dojo.experimental("dojox.widget.Standby");

dojo.declare("dojox.widget.Standby",[dijit._Widget, dijit._Templated],{
	//	summary:
	//		A widget designed to act as a Standby/Busy/Disable/Blocking widget to indicate a 
	//		particular DOM node is processing and cannot be clicked on at this time.
	//		This widget uses absolute positioning to apply the overlay and image.
	// 
	//	image:	
	//		A URL to an image to center within the blocking overlay.  The default is a basic spinner.
	//
	//	imageText:
	//		Text to set on the ALT tag of the image.  The default is 'Please wait...'
	//
	//	color:
	//		The color to use for the translucent overlay.  Text string such as: darkblue, #FE02FD, etc.
	templatePath: dojo.moduleUrl("dojox", "widget/Standby/Standby.html"),

	_underlayNode: null,	//The node that is the translucent underlay for the image that blocks access to the target.

	_imageNode: null,		// The image node where we attach and define the image to display.

	image: dojo.moduleUrl("dojox", "widget/Standby/images/loading.gif").toString(), //The image

	imageText: "Please Wait...", //Text for the ALT tag.

	_displayed: false, //display status

	_resizeCheck: null, //Handle to interval function that chects the target for changes.
	
	target: "", //The target to overlay when active.  Can be a widget id, a dom id, or a direct node reference.

	color: "#C0C0C0",  //Default color for the translucent overlay.  (light gray.)

	startup: function(args){
		//	summary:
		//		Over-ride of the basic widget startup function.  Configures the target node and sets the image to use.
		if(typeof this.target === "string"){
			var w = dijit.byId(this.target);
			if(w){
				this.target = w.domNode;
			}else{
				this.target = dojo.byId(this.target);
			}
		}
		dojo.style(this._underlayNode, "display", "none");
		dojo.style(this._imageNode, "display", "none");
		dojo.style(this._underlayNode, "backgroundColor", this.color);
		dojo.attr(this._imageNode, "src", this.image);
		dojo.attr(this._imageNode, "alt", this.imageText);
		this.connect(this._underlayNode, "onclick", "_ignore");

		//Last thing to do is move the widgets parent, if any, to the current document body.  Avoids having to deal with
		//parent relative/absolute mess.  Otherwise positioning goes goofy.
		if(this.domNode.parentNode && this.domNode.parentNode != dojo.body()){
			dojo.body().appendChild(this.domNode);
		} 
	},

	show: function() {
		//	summary:
		//		Function to display the blocking overlay and busy/status icon
		if(!this._displayed){
			this._displayed = true;
			this._size();
			this._fadeIn();
		}
	},

	hide: function(){
		//	summary:
		//		Function to hide the blocking overlay and status icon.
		if(this._displayed){
			this._size();
			this._fadeOut();
			this._displayed = false;
			if (this._resizeCheck !== null) {
				clearInterval(this._resizeCheck);
				this._resizeCheck = null;
			}
		}
	},

	_size: function(){
		//	summary:
		//		Internal function that handles resizing the overlay and centering of the image on window resizing.
		if(this._displayed){
			//Show the image and make sure the zIndex is set high.
			var curStyle = dojo.style(this._imageNode, "display"); 
			dojo.style(this._imageNode, "display", "block");
			var box = dojo.coords(this.target);
			var img = dojo.marginBox(this._imageNode);
			dojo.style(this._imageNode, "display", curStyle);
			dojo.style(this._imageNode, "zIndex", "10000");

			//Need scroll positions as it needs to alter ABS positioning.
			var sVal = dojo._docScroll();
			if(!sVal){
				sVal = {x:0,y:0};
			}

			//Address margins as they shift the position..
			var marginLeft = dojo.style(this.target, "marginLeft");
			if(dojo.isWebKit && marginLeft){
				//Webkit works differently here.  Needs to be doubled.
				//Don't ask me why. :)
				marginLeft = marginLeft*2;
			}

			if(marginLeft){
				box.w = box.w - marginLeft;
			}
 		
			if (!dojo.isWebKit) {
				//Webkit and others work differently here.  
				var marginRight = dojo.style(this.target, "marginRight");
				if(marginRight){
					box.w = box.w - marginRight;
				}
    		}

			var marginTop = dojo.style(this.target, "marginTop");
			if(marginTop){
				box.h = box.h - marginTop;
			}
			var marginBottom = dojo.style(this.target, "marginBottom");
			if(marginBottom){
				box.h = box.h - marginBottom;
			}

			if(box.h > 0 && box.w > 0){
				//Set position and size of the blocking div overlay.
				dojo.style(this._underlayNode, "width", box.w + "px");
				dojo.style(this._underlayNode, "height", box.h + "px");
				dojo.style(this._underlayNode, "top", (box.y + sVal.y) + "px");
				dojo.style(this._underlayNode, "left", (box.x + sVal.x) + "px");


				//Apply curving styles if present.
				var cloneStyles = function(list, scope){
					dojo.forEach(list, function(style){
						dojo.style(this._underlayNode,style,dojo.style(this.target,style));
					}, scope);
				};
				var styles = ["borderRadius", "borderTopLeftRadius", "borderTopRightRadius","borderBottomLeftRadius", "borderBottomRightRadius"];
				cloneStyles(styles, this);
				if(!dojo.isIE){
					//Browser specific styles to try and clone if non-IE.
					styles = ["MozBorderRadius", "MozBorderRadiusTopleft", "MozBorderRadiusTopright","MozBorderRadiusBottomleft", "MozBorderRadiusBottomright",
						"WebkitBorderRadius", "WebkitBorderTopLeftRadius", "WebkitBorderTopRightRadius", "WebkitBorderBottomLeftRadius","WebkitBorderBottomRightRadius"
					];
					cloneStyles(styles, this);
				}
				var imgTop = (box.h/2) - (img.h/2);
				var imgLeft = (box.w/2) - (img.w/2);
				dojo.style(this._imageNode, "top", (imgTop + box.y + sVal.y) + "px");
				dojo.style(this._imageNode, "left", (imgLeft + box.x + sVal.x) + "px");
				dojo.style(this._underlayNode, "display", "block");
				dojo.style(this._imageNode, "display", "block");
			}else{
				//Target has no size, display nothing on it!
				dojo.style(this._underlayNode, "display", "none");
				dojo.style(this._imageNode, "display", "none");
			}
			if (this._resizeCheck === null) {
				//Set an interval timer that checks the target size and scales as needed.
				//Checking every 10th of a second seems to generate a fairly smooth update.
				var self = this;
				this._resizeCheck = setInterval(function(){self._size();}, 100);
			}
		}
	},

	_fadeIn: function(){
		//	summary:
		//		Internal function that does the opacity style fade in animation.
		var underlayNodeAnim = dojo.animateProperty({node: this._underlayNode, properties: {opacity: {start: 0, end: 0.75}}});
		var imageAnim = dojo.animateProperty({node: this._imageNode, properties: {opacity: {start: 0, end: 1}}});
		var anim = dojo.fx.combine([underlayNodeAnim,imageAnim]);
		anim.play();
	},

	_fadeOut: function(){
		//	summary:
		//		Internal function that does the opacity style fade out animation.
		var self = this;
		var underlayNodeAnim = dojo.animateProperty({
			node: this._underlayNode, 
			properties: {opacity: {start: 0.75, end: 0}},
			onEnd: function() {
				dojo.style(self._underlayNode, "display", "none");
			}
		});
		var imageAnim = dojo.animateProperty({
			node: this._imageNode, 
			properties: {opacity: {start: 1, end: 0}},
			onEnd: function() {
				dojo.style(self._imageNode, "display", "none");
			}
		});
		var anim = dojo.fx.combine([underlayNodeAnim,imageAnim]);
		anim.play();
	},

	_ignore: function(event){
		 if(event){
			 event.preventDefault();
			 event.stopPropagation();
		 }
	},

	uninitialize: function(){
		//	summary:	
		//		Over-ride to hide the widget, which clears intervals, before cleanup.
		this.hide();
	}

});	
