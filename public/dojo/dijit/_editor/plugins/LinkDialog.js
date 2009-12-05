dojo.provide("dijit._editor.plugins.LinkDialog");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit._editor._Plugin");
dojo.require("dijit.TooltipDialog");
dojo.require("dijit.form.Button");
dojo.require("dijit.form.ValidationTextBox");
dojo.require("dojo.i18n");
dojo.require("dojo.string");
dojo.requireLocalization("dijit._editor", "LinkDialog");

dojo.declare("dijit._editor.plugins.LinkDialog",
	dijit._editor._Plugin,
	{
		//	summary:
		//		This plugin provides dialogs for inserting links and images into the editor
		//
		//	description:
		//		The commands provided by this plugin are:
		//		* createLink
		//		* insertImage

		// Override _Plugin.buttonClass.   This plugin is controlled by a DropDownButton
		// (which triggers a TooltipDialog).
		buttonClass: dijit.form.DropDownButton,

		// Override _Plugin.useDefaultCommand... processing is handled by this plugin, not by dijit.Editor.
		useDefaultCommand: false,

		// urlRegExp: [protected] String
		//		Used for validating input as correct URL
		urlRegExp: "((https?|ftps?)\\://|)(((?:(?:[\\da-zA-Z](?:[-\\da-zA-Z]{0,61}[\\da-zA-Z])?)\\.)*(?:[a-zA-Z](?:[-\\da-zA-Z]{0,6}[\\da-zA-Z])?)\\.?)|(((\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.){3}(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])|(0[xX]0*[\\da-fA-F]?[\\da-fA-F]\\.){3}0[xX]0*[\\da-fA-F]?[\\da-fA-F]|(0+[0-3][0-7][0-7]\\.){3}0+[0-3][0-7][0-7]|(0|[1-9]\\d{0,8}|[1-3]\\d{9}|4[01]\\d{8}|42[0-8]\\d{7}|429[0-3]\\d{6}|4294[0-8]\\d{5}|42949[0-5]\\d{4}|429496[0-6]\\d{3}|4294967[01]\\d{2}|42949672[0-8]\\d|429496729[0-5])|0[xX]0*[\\da-fA-F]{1,8}|([\\da-fA-F]{1,4}\\:){7}[\\da-fA-F]{1,4}|([\\da-fA-F]{1,4}\\:){6}((\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.){3}(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])))(\\:\\d+)?(/(?:[^?#\\s/]+/)*(?:[^?#\\s/]+(?:\\?[^?#\\s/]*)?(?:#[A-Za-z][\\w.:-]*)?)?)?",

		// linkDialogTemplate: [protected] String
		//		Template for contents of TooltipDialog to pick URL
		linkDialogTemplate: [
			"<table><tr><td>",
			"<label for='${id}_urlInput'>${url}</label>",
			"</td><td>",
			"<input dojoType='dijit.form.ValidationTextBox' regExp='${urlRegExp}' required='true' id='${id}_urlInput' name='urlInput'>",
			"</td></tr><tr><td>",
			"<label for='${id}_textInput'>${text}</label>",
			"</td><td>",
			"<input dojoType='dijit.form.ValidationTextBox' required='true' id='${id}_textInput' name='textInput'>",
			"</td></tr><tr><td colspan='2'>",
			"<button dojoType='dijit.form.Button' type='submit'>${set}</button>",
			"</td></tr></table>"
		].join(""),

		_initButton: function(){
			// Override _Plugin._initButton() to initialize DropDownButton and TooltipDialog.
			var _this = this;
			this.tag = this.command == 'insertImage' ? 'img' : 'a';
			var messages = dojo.i18n.getLocalization("dijit._editor", "LinkDialog", this.lang);
			var dropDown = (this.dropDown = new dijit.TooltipDialog({
				title: messages[this.command + "Title"],
				execute: dojo.hitch(this, "setValue"),
				onOpen: function(){
					_this._onOpenDialog();
					dijit.TooltipDialog.prototype.onOpen.apply(this, arguments);
				},
				onCancel: function(){
					setTimeout(dojo.hitch(_this, "_onCloseDialog"),0);
				},
				onClose: dojo.hitch(this, "_onCloseDialog")
			}));
			messages.urlRegExp = this.urlRegExp;
			messages.id = dijit.getUniqueId(this.editor.id);
			this._setContent(dropDown.title + "<div style='border-bottom: 1px black solid;padding-bottom:2pt;margin-bottom:4pt'></div>" + dojo.string.substitute(this.linkDialogTemplate, messages));
			dropDown.startup();

			this.inherited(arguments);
		},

		_setContent: function(staticPanel){
			// summary:
			//		Helper for _initButton above.   Not sure why it's a separate method.
			this.dropDown.attr('content', staticPanel);
		},

		setValue: function(args){
			// summary:
			//		Callback from the dialog when user presses "set" button.
			// tags:
			//		private

			//TODO: prevent closing popup if the text is empty
			this._onCloseDialog();
			if(dojo.isIE){ //see #4151
				var a = dojo.withGlobal(this.editor.window, "getAncestorElement", dijit._editor.selection, [this.tag]);
				if(a){
					dojo.withGlobal(this.editor.window, "selectElement", dijit._editor.selection, [a]);
				}
			}
			args.tag = this.tag;
			args.refAttr = this.tag == 'img' ? 'src' : 'href';
			//TODO: textInput should be formatted by escapeXml
			var template = "<${tag} ${refAttr}='${urlInput}' _djrealurl='${urlInput}'" +
				(args.tag == 'img' ? " alt='${textInput}'>" : ">${textInput}") +
				"</${tag}>";
			this.editor.execCommand('inserthtml', dojo.string.substitute(template, args));
 		},

		_onCloseDialog: function(){
			// summary:
			//		Handler for close event on the dialog
			this.editor.focus();
		},

		_onOpenDialog: function(){
			// summary:
			//		Handler for when the dialog is opened.
			//		If the caret is currently in a URL then populate the URL's info into the dialog.

			var a = dojo.withGlobal(this.editor.window, "getAncestorElement", dijit._editor.selection, [this.tag]);
			var url, text;
			if(a){
				url = a.getAttribute('_djrealurl');
				text = this.tag == 'img' ? a.getAttribute('alt') : a.textContent || a.innerText;
				dojo.withGlobal(this.editor.window, "selectElement", dijit._editor.selection, [a, true]);
			}else{
				text = dojo.withGlobal(this.editor.window, dijit._editor.selection.getSelectedText);
			}

			this.dropDown.reset();
			this.dropDown.setValues({urlInput: url || '', textInput: text || ''});
			//dijit.focus(this.urlInput);
		}/*,

//TODO we don't show this state anymore
		updateState: function(){
			// summary: change shading on button if we are over a link (or not)

			var _e = this.editor;
			if(!_e || !_e.isLoaded){ return; }
			if(this.button){
				// display button differently if there is an existing link associated with the current selection
				var hasA = dojo.withGlobal(this.editor.window, "hasAncestorElement", dijit._editor.selection, [this.tag]);
				this.button.attr('checked', hasA);
			}
		}
*/
	}
);

// Register this plugin.
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin",null,function(o){
	if(o.plugin){ return; }
	switch(o.args.name){
	case "createLink": case "insertImage":
		o.plugin = new dijit._editor.plugins.LinkDialog({command: o.args.name});
	}
});
