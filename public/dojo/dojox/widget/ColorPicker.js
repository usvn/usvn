dojo.provide("dojox.widget.ColorPicker");
dojo.experimental("dojox.widget.ColorPicker"); // level: beta

dojo.require("dijit.form._FormWidget");
dojo.require("dojo.dnd.move"); 
dojo.require("dojo.fx"); 
dojo.require("dojox.color");

;(function(d){
	
	var webSafeFromHex = function(hex){
		// stub, this is planned later:
		return hex;
	}
	
	dojo.declare("dojox.widget.ColorPicker",
		dijit.form._FormWidget,
		{
		// summary: a HSV color picker - similar to Photoshop picker
		//
		// description: 
		//		Provides an interactive HSV ColorPicker similar to
		//		PhotoShop's color selction tool. This is an enhanced 
		//		version of the default dijit.ColorPalette, though provides
		//		no accessibility.
		//
		// example:
		// |	var picker = new dojox.widget.ColorPicker({
		// |		// a couple of example toggles:
		// |		animatePoint:false,
		// |		showHsv: false,
		// |		webSafe: false,
		// |		showRgb: false 	
		// |	});
		//	
		// example: 
		// | 	<!-- markup: -->
		// | 	<div dojoType="dojox.widget.ColorPicker"></div>
		//
		// showRgb: Boolean
		//	show/update RGB input nodes
		showRgb: true,
	
		// showHsv: Boolean
		//	show/update HSV input nodes
		showHsv: true,
	
		// showHex: Boolean
		//	show/update Hex value field
		showHex: true,

		// webSafe: Boolean
		//	deprecated? or just use a toggle to show/hide that node, too?
		webSafe: true,

		// animatePoint: Boolean
		//	toggle to use slideTo (true) or just place the cursor (false) on click
		animatePoint: true,

		// slideDuration: Integer
		//	time in ms picker node will slide to next location (non-dragging) when animatePoint=true
		slideDuration: 250, 

		// liveUpdate: Boolean
		//		Set to true to fire onChange in an indeterminate way
		liveUpdate: false, 

		// PICKER_HUE_H: int
		//     Height of the hue picker, used to calculate positions    
		PICKER_HUE_H: 150,
		
		// PICKER_SAT_VAL_H: int
		//     Height of the 2d picker, used to calculate positions    
		PICKER_SAT_VAL_H: 150,
		
		// PICKER_SAT_VAL_W: int
		//     Width of the 2d picker, used to calculate positions    
		PICKER_SAT_VAL_W: 150,
		
		// value: String
		//	Default color for this component. Only hex values are accepted as incoming/returned
		//	values. Adjust this value with `.attr`, eg: dijit.byId("myPicker").attr("value", "#ededed");
		//	to cause the points to adjust and the values to reflect the current color. 
		value: "#ffffff",
		
		_underlay: d.moduleUrl("dojox.widget","ColorPicker/images/underlay.png"),
		// don't change to d.moduleUrl, build won't intern it.
		templatePath: dojo.moduleUrl("dojox.widget","ColorPicker/ColorPicker.html"),
		
		postCreate: function(){
			this.inherited(arguments);

			// summary: As quickly as we can, set up ie6 alpha-filter support for our
			// 	underlay.  we don't do image handles (done in css), just the 'core' 
			//	of this widget: the underlay. 
			if(d.isIE < 7){ 
				this.colorUnderlay.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+this._underlay+"', sizingMethod='scale')";
				this.colorUnderlay.src = this._blankGif.toString();
			}
			// hide toggle-able nodes:
			if(!this.showRgb){ this.rgbNode.style.display = "none"; }
			if(!this.showHsv){ this.hsvNode.style.display = "none"; }
			if(!this.showHex){ this.hexNode.style.display = "none"; } 
			if(!this.webSafe){ this.safePreviewNode.style.visibility = "hidden"; } 
			
			// this._offset = ((d.marginBox(this.cursorNode).w)/2); 
			this._offset = 0; 
			var cmb = d.marginBox(this.cursorNode);
			var hmb = d.marginBox(this.hueCursorNode);

			this._shift = {
				hue: {
					x: Math.round(hmb.w / 2) - 1,
					y: Math.round(hmb.h / 2) - 1
				},
				picker: {
					x: Math.floor(cmb.w / 2),
					y: Math.floor(cmb.h / 2)
				}
			};
			
			//setup constants
			this.PICKER_HUE_H = d.coords(this.hueNode).h;
			
			var cu = d.coords(this.colorUnderlay);
			this.PICKER_SAT_VAL_H = cu.h;
			this.PICKER_SAT_VAL_W = cu.w;
			
			var ox = this._shift.picker.x;
			var oy = this._shift.picker.y;
			this._mover = new d.dnd.move.boxConstrainedMoveable(this.cursorNode, {
				box: {
					t:0 - oy,
					l:0 - ox,
					w:this.PICKER_SAT_VAL_W,
					h:this.PICKER_SAT_VAL_H
				}
			});
			
			this._hueMover = new d.dnd.move.boxConstrainedMoveable(this.hueCursorNode, {
				box: {
					t:0 - this._shift.hue.y,
					l:0,
					w:0,
					h:this.PICKER_HUE_H
				}
			});
			
			// no dnd/move/move published ... use a timer:
			d.subscribe("/dnd/move/stop", d.hitch(this, "_clearTimer"));
			d.subscribe("/dnd/move/start", d.hitch(this, "_setTimer"));
			
		},
		
		startup: function(){
			this._started = true;
			this.attr("value", this.value);
		},
		
		_setValueAttr: function(value){
			if(!this._started){ return; }
			this.setColor(value, true);
		},
		
		setColor: function(/* String */color, force){
			// summary: Set a color on a picker. Usually used to set
			//          initial color as an alternative to passing defaultColor option
			//          to the constructor. 
			var col = dojox.color.fromString(color);
			
			this._updatePickerLocations(col);
			this._updateColorInputs(col);
			this._updateValue(col, force);
		},
		
		_setTimer: function(/* d.dnd.Mover */mover){
			// FIXME: should I assume this? focus on mouse down so on mouse up
			dijit.focus(mover.node);
			d.setSelectable(this.domNode,false);
			this._timer = setInterval(d.hitch(this, "_updateColor"), 45);	
		},
		
		_clearTimer: function(/* d.dnd.Mover */mover){
			clearInterval(this._timer);
			this._timer = null;
			this.onChange(this.value);
			d.setSelectable(this.domNode,true);
		},
		
		_setHue: function(/* Decimal */h){
			// summary: sets a natural color background for the 
			// 	underlay image against closest hue value (full saturation) 
			// h: 0..360 
			d.style(this.colorUnderlay, "backgroundColor", dojox.color.fromHsv(h,100,100).toHex());
			
		},
		
		_updateColor: function(){
			// summary: update the previewNode color, and input values [optional]
			
			var _huetop = d.style(this.hueCursorNode,"top") + this._shift.hue.y, 
				_pickertop = d.style(this.cursorNode,"top") + this._shift.picker.y,
				_pickerleft = d.style(this.cursorNode,"left") + this._shift.picker.x,
			
				h = Math.round(360 - (_huetop / this.PICKER_HUE_H * 360)),
				col = dojox.color.fromHsv(h, _pickerleft / this.PICKER_SAT_VAL_W * 100, 100 - (_pickertop / this.PICKER_SAT_VAL_H * 100))
			;

			
			this._updateColorInputs(col);
			this._updateValue(col, true);
			
			// update hue, not all the pickers
			if (h!=this._hue) {
				this._setHue(h);
			}
		},
		
		_colorInputChange: function(e){
			//summary: updates picker position and inputs 
			//         according to rgb, hex or hsv input changes

			var col, hasit = false;
			switch (e.target) {
				//transform to hsv to pixels

				case this.hexCode:
					col = dojox.color.fromString(e.target.value);
					hasit = true;
					
					break;
				case this.Rval:
				case this.Gval:
				case this.Bval:
					col = dojox.color.fromArray([this.Rval.value, this.Gval.value, this.Bval.value]);
					hasit = true;
					break;
				case this.Hval:
				case this.Sval:
				case this.Vval:
					col = dojox.color.fromHsv(this.Hval.value, this.Sval.value, this.Vval.value);
					hasit = true;
					break
			}
			
			if(hasit){
				this._updatePickerLocations(col);
				this._updateColorInputs(col);
				this._updateValue(col, true);
			}
			
		},
		
		_updateValue: function(/* dojox.color.Color */col, /* Boolean */fireChange){
			// summary: updates the value of the widget
			//          can cancel reverse onChange by specifying second param
			var hex = col.toHex();
			
			this.value = this.valueNode.value = hex;
			
			// anytime we muck with the color, fire onChange?
			if(fireChange && (!this._timer || this.liveUpdate)) {
				this.onChange(hex);
			}
		},
		
		_updatePickerLocations: function(/* dojox.color.Color */col){
			//summary: update handles on the pickers acording to color values
			//  
			var hsv = col.toHsv(),
				ypos = Math.round(this.PICKER_HUE_H - hsv.h / 360 * this.PICKER_HUE_H - this._shift.hue.y),
				newLeft = Math.round(hsv.s / 100 * this.PICKER_SAT_VAL_W - this._shift.picker.x),
				newTop = Math.round(this.PICKER_SAT_VAL_H - hsv.v / 100 * this.PICKER_SAT_VAL_H - this._shift.picker.y)
			;
			
			if (this.animatePoint) {
				d.fx.slideTo({
					node: this.hueCursorNode,
					duration: this.slideDuration,
					top: ypos,
					left: 0
				}).play();
				
				d.fx.slideTo({
					node: this.cursorNode,
					duration: this.slideDuration,
					top: newTop,
					left: newLeft
				}).play();
				
			}
			else {
				d.style(this.hueCursorNode, "top", ypos + "px");
				d.style(this.cursorNode, {
					left: newLeft + "px",
					top: newTop + "px"
				});
			}
			
			// limit hue calculations to only when it changes
			if (hsv.h != this._hue) {
				this._setHue(hsv.h);
			}
			
		},
		
		_updateColorInputs: function(/* dojox.color.Color */col){
			//summary: updates color inputs that were changed through other inputs
			//or by clicking on the picker
			
			var hex = col.toHex();
			
			if (this.showRgb) {
				this.Rval.value = col.r;
				this.Gval.value = col.g;
				this.Bval.value = col.b;
			}
			
			if (this.showHsv) {
				var hsv = col.toHsv();
				this.Hval.value = Math.round((hsv.h)); // convert to 0..360
				this.Sval.value = Math.round(hsv.s);
				this.Vval.value = Math.round(hsv.v);
			}
			
			if (this.showHex) {
				this.hexCode.value = hex;
			}
			
			this.previewNode.style.backgroundColor = hex;
			
			if (this.webSafe) {
				this.safePreviewNode.style.backgroundColor = webSafeFromHex(hex);
			}
		},
		
		_setHuePoint: function(/* Event */evt){ 
			// summary: set the hue picker handle on relative y coordinates
			var ypos = evt.layerY - this._shift.hue.y;
			if(this.animatePoint){
				d.fx.slideTo({ 
					node: this.hueCursorNode, 
					duration:this.slideDuration,
					top: ypos,
					left: 0,
					onEnd: d.hitch(this, "_updateColor", true)
				}).play();
			}else{
				d.style(this.hueCursorNode, "top", ypos + "px");
				this._updateColor(false); 
			}
		},
		
		_setPoint: function(/* Event */evt){
			// summary: set our picker point based on relative x/y coordinates
			//  evt.preventDefault();
			var newTop = evt.layerY - this._shift.picker.y,
				newLeft = evt.layerX - this._shift.picker.x
			;
			if(evt){ dijit.focus(evt.target); }

			if(this.animatePoint){
				d.fx.slideTo({ 
					node: this.cursorNode, 
					duration: this.slideDuration,
					top: newTop,
					left: newLeft,
					onEnd: d.hitch(this,"_updateColor", true)
				}).play();
			}else{
				d.style(this.cursorNode, {
					left: newLeft + "px",
					top: newTop + "px"	
				});
				this._updateColor(false); 
			}
		},
		
		_handleKey: function(/* Event */e){
			// FIXME: not implemented YET
			// var keys = d.keys;
		}
		
	});
	
})(dojo);
