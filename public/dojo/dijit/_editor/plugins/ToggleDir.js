dojo.provide("dijit._editor.plugins.ToggleDir");
dojo.experimental("dijit._editor.plugins.ToggleDir");

dojo.require("dijit._editor._Plugin");

dojo.declare("dijit._editor.plugins.ToggleDir",
	dijit._editor._Plugin,
	{
		//summary:
		//		This plugin is used to toggle direction of the edited document only,
		//		no matter what direction the whole page is.

		// TODO: like TabIndent plugin, this should probably be using ToggleButton

		// Override _Plugin.useDefaultCommand: processing is done in this plugin rather than by sending command to
		// the Editor
		useDefaultCommand: false,

		// TODO: unclear if this is needed.
		command: "toggleDir",

		_initButton: function(){
			// Override _Plugin._initButton() to setup handler for button click events.
			this.inherited("_initButton", arguments);
			this.connect(this.button, "onClick", this._toggleDir);		
		},

		updateState: function(){
			// Override _Plugin.updateState() to do nothing, since we don't need to react to changes in the
			// editor like arrow keys etc.
		},

		_toggleDir: function(){
			// summary:
			//		Handler for button click events, to switch the text direction of the editor
			var editDoc = this.editor.editorObject.contentWindow.document.documentElement;
			var isLtr = dojo.getComputedStyle(editDoc).direction == "ltr";
			editDoc.dir/*html node*/ = isLtr ? "rtl" : "ltr";
		}
	}
);

// Register this plugin.
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin",null,function(o){
	if(o.plugin){ return; }
	switch(o.args.name){
	case "toggleDir":
		o.plugin = new dijit._editor.plugins.ToggleDir({command: o.args.name});
	}
});
