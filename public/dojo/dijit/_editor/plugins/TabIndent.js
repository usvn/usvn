dojo.provide("dijit._editor.plugins.TabIndent");
dojo.experimental("dijit._editor.plugins.TabIndent");

dojo.require("dijit._editor._Plugin");

dojo.declare("dijit._editor.plugins.TabIndent",
	dijit._editor._Plugin,
	{
		// summary:
		//		This plugin is used to allow the use of the tab and shift-tab keys
		//		to indent/outdent list items.  This overrides the default behavior
		//		of moving focus from/to the toolbar
		
		// Override _Plugin.useDefaultCommand... processing is handled by this plugin, not by dijit.Editor.
		useDefaultCommand: false,

		// Override _Plugin.buttonClass to use a ToggleButton for this plugin rather than a vanilla Button
		buttonClass: dijit.form.ToggleButton,

		// TODO: unclear if this is needed
		command: "tabIndent",

		_initButton: function(){
			// Override _Plugin._initButton() to setup listener on button click
			this.inherited("_initButton", arguments);
			this.connect(this.button, "onClick", this._tabIndent);

			// TODO: set initial checked state of button based on Editor.isTabIndent?
		},

		updateState: function(){
			// Overrides _Plugin.updateState().
			// Since (apparently) Ctrl-m in the editor will switch tabIndent mode on/off, we need to react to that.

			// TODO: can't all this code be replaced by a simple:
			//		this.button.attr('checked', this.editor.isTabIndent);

			var _e = this.editor;
			var _c = this.command;
			if(!_e){ return; }
			if(!_e.isLoaded){ return; }
			if(!_c.length){ return; }
			if(this.button){
				try{
					var enabled = _e.isTabIndent;
					if(typeof this.button.checked == 'boolean'){ 
						this.button.attr('checked', enabled);
					}
				}catch(e){
					console.debug(e);
				}
			}
		},

		_tabIndent: function(){
			// summary:
			//		Handler for checking/unchecking the toggle button.
			//		Toggle the value for isTabIndent.

			// TODO: probably better to connect to ToggleButton.onChange and then use the reported value, ex:
			//		var e = this.editor;
			//		this.connect(this.button, "onChange", function(val){ e.isTabIndent = val; })

			this.editor.isTabIndent = !this.editor.isTabIndent;
		}
	}
);

// Register this plugin.
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin",null,function(o){
	if(o.plugin){ return; }
	switch(o.args.name){
	case "tabIndent":
		o.plugin = new dijit._editor.plugins.TabIndent({command: o.args.name});
	}
});
