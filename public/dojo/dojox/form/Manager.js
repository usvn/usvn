dojo.provide("dojox.form.Manager");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.require("dojox.form.manager._Mixin");
dojo.require("dojox.form.manager._NodeMixin");
dojo.require("dojox.form.manager._FormMixin");
dojo.require("dojox.form.manager._ValueMixin");
dojo.require("dojox.form.manager._EnableMixin");
dojo.require("dojox.form.manager._DisplayMixin");
dojo.require("dojox.form.manager._ClassMixin");

dojo.declare("dojox.form.Manager", [
		dijit._Widget, dijit._Templated,
		dojox.form.manager._Mixin,
		dojox.form.manager._NodeMixin,
		dojox.form.manager._FormMixin,
		dojox.form.manager._ValueMixin,
		dojox.form.manager._EnableMixin,
		dojox.form.manager._DisplayMixin,
		dojox.form.manager._ClassMixin
], {
	// summary:
	//		The widget to orchestrate dynamic forms.
	// description:
	//		This widget hosts dojox.form.manager mixins.
	//		See dojox.form.manager._Mixin for more info.

	widgetsInTemplate: true,

	buildRendering: function(){
		var node = this.domNode = this.srcNodeRef;
		if(!this.containerNode){
			// all widgets with descendants must set containerNode
				this.containerNode = node;
		}
		this._attachTemplateNodes(node);
	},

	startup: function(){
		if(this._started){ return; }
		this._attachTemplateNodes(this.getDescendants(), function(n,p){ return n[p]; });
		this.inherited(arguments);
	}
});
