dojo.provide("dijit.form.VerticalSlider");

dojo.require("dijit.form.HorizontalSlider");

dojo.declare(
	"dijit.form.VerticalSlider",
	dijit.form.HorizontalSlider,
{
	// summary:
	//		A form widget that allows one to select a value with a vertically draggable handle

	templatePath: dojo.moduleUrl('dijit.form','templates/VerticalSlider.html'),
	_mousePixelCoord: "pageY",
	_pixelCount: "h",
	_startingPixelCoord: "y",
	_startingPixelCount: "t",
	_handleOffsetCoord: "top",
	_progressPixelSize: "height",

	// _descending: Boolean
	//	   Specifies if the slider values go from high-on-top (true), or low-on-top (false)
	//	TODO: expose this in 1.2 - the css progress/remaining bar classes need to be reversed
	_descending: true,

	startup: function(){
		if(this._started){ return; }

		if(!this.isLeftToRight() && dojo.isMoz){
			if(this.leftDecoration){this._rtlRectify(this.leftDecoration);}
			if(this.rightDecoration){this._rtlRectify(this.rightDecoration);}
		}

		this.inherited(arguments);
	},
		
	_isReversed: function(){
		// summary:
		//		Overrides HorizontalSlider._isReversed.
		//		Indicates if values are high on top (with low numbers on the bottom).
		return this._descending;
	},

	_rtlRectify: function(decorationNode/*NodeList*/){
		// summary:
		//	    Helper function on gecko.
		//		Rectify children nodes for left/right decoration in rtl case.
		//		Simply switch the rule and label child for each decoration node.
		// tags:
		//		private
		var childNodes = [];
		while(decorationNode.firstChild){
				childNodes.push(decorationNode.firstChild);
				decorationNode.removeChild(decorationNode.firstChild);
		}
		for(var i = childNodes.length-1; i >=0; i--){
			if(childNodes[i]){
				decorationNode.appendChild(childNodes[i]);
			}
		}
	}
});

