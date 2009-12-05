dojo.provide("dijit._tree.dndSelector");
dojo.require("dojo.dnd.common");
dojo.require("dijit._tree.dndContainer");

dojo.declare("dijit._tree.dndSelector",
	dijit._tree.dndContainer,
	{
		// summary:
		//		This is a base class for `dijit._tree.dndSource` , and isn't meant to be used directly.
		//		It's based on `dojo.dnd.Selector`.
		// tags:
		//		protected

		constructor: function(tree, params){
			// summary:
			//		Initialization
			// tags:
			//		private

			this.selection={};
			this.anchor = null;
			this.simpleSelection=false;
		
			this.events.push(
				dojo.connect(this.tree.domNode, "onmousedown", this,"onMouseDown"),
				dojo.connect(this.tree.domNode, "onmouseup", this,"onMouseUp")
			);
		},
	
		// singular: [readonly] Boolean
		//		Apparently this is indicates whether a single or multiple elements are
		//		selected, but AFAIK Tree doesn't support multiple selection, so it doesn't
		//		do anything.   (There is, however, a bunch of dead code that would only run
		//		if singular == true)
		singular: false,	// is singular property
	
		// methods

		getSelectedItems: function(){
			// summary:
			//		Returns selected items, for which there is only one for Tree?
			// tags:
			//		private

			// TODO: apparently no one is calling this; get rid of it?

			var selectedItems = [];
			for (var i in this.selection){
				selectedItems.push(dijit.getEnclosingWidget(this.selection[i]).item);
			}
			return selectedItems;
		},

		getSelectedNodes: function(){
			// summary:
			//		Returns the set of selected nodes.
			//		Used by dndSource on the start of a drag.
			// tags:
			//		protected
			return this.selection;
		},

		selectNone: function(){
			// summary:
			//		Unselects all items
			// tags:
			//		private

			return this._removeSelection()._removeAnchor();	// self
		},

		insertItems: function(item, parent){
			// summary:
			//		Inserts new data items (see Container's insertNodes method for details).
			//		Apparently an unused method.
			// tags:
			//		private

			// TODO: this isn't used anywhere, delete it
			
			//we actually need to add things to the store here instead of adding nodes directly to the tree		
		},

		destroy: function(){
			// summary:
			//		Prepares the object to be garbage-collected
			dijit._tree.dndSelector.superclass.destroy.call(this);
			this.selection = this.anchor = null;
		},

		// mouse events
		onMouseDown: function(e){
			// summary:
			//		Event processor for onmousedown
			// e: Event
			//		mouse event
			// tags:
			//		protected
			
			if(!this.current){ return; }

			if(e.button == 2){ return; }	// ignore right-click

			var item = dijit.getEnclosingWidget(this.current).item;
			var id = this.tree.model.getIdentity(item);

			if (!this.current.id) {
				this.current.id=id;
			}

			if (!this.current.type) {
				this.current.type="data";
			}

			if(!this.singular && !dojo.dnd.getCopyKeyState(e) && !e.shiftKey && (this.current.id in this.selection)){
				this.simpleSelection = true;
				dojo.stopEvent(e);
				return;
			}
			if(this.singular){
				if(this.anchor == this.current){
					if(dojo.dnd.getCopyKeyState(e)){
						this.selectNone();
					}
				}else{
					this.selectNone();
					this.anchor = this.current;
					this._addItemClass(this.anchor, "Anchor");

					this.selection[this.current.id] = this.current;
				}
			}else{
				if(!this.singular && e.shiftKey){	
					if (dojo.dnd.getCopyKeyState(e)){
						//TODO add range to selection
					}else{
						//TODO select new range from anchor 
					}
				}else{
					if(dojo.dnd.getCopyKeyState(e)){
						if(this.anchor == this.current){
							delete this.selection[this.anchor.id];
							this._removeAnchor();
						}else{
							if(this.current.id in this.selection){
								this._removeItemClass(this.current, "Selected");
								delete this.selection[this.current.id];
							}else{
								if(this.anchor){
									this._removeItemClass(this.anchor, "Anchor");
									this._addItemClass(this.anchor, "Selected");
								}
								this.anchor = this.current;
								this._addItemClass(this.current, "Anchor");
								this.selection[this.current.id] = this.current;
							}
						}
					}else{
					    // TODO: item and id are already declared and set above, remove these lines?
						var item = dijit.getEnclosingWidget(this.current).item;
						var id = this.tree.model.getIdentity(item);
						if(!(id in this.selection)){
							this.selectNone();
							this.anchor = this.current;
							this._addItemClass(this.current, "Anchor");
							this.selection[id] = this.current;
						}
					}
				}
			}

			dojo.stopEvent(e);
		},

		onMouseUp: function(e){
			// summary:
			//		Event processor for onmouseup
			// e: Event
			//		mouse event
			// tags:
			//		protected
			if(!this.simpleSelection){ return; }
			this.simpleSelection = false;
			this.selectNone();
			if(this.current){
				this.anchor = this.current;
				this._addItemClass(this.anchor, "Anchor");
				this.selection[this.current.id] = this.current;
			}
		},
		_removeSelection: function(){
			// summary:
			//		Unselects all items
			// tags:
			//		private
			var e = dojo.dnd._empty;
			for(var i in this.selection){
				if(i in e){ continue; }
				var node = dojo.byId(i);
				if(node){ this._removeItemClass(node, "Selected"); }
			}
			this.selection = {};
			return this;	// self
		},

		_removeAnchor: function(){
			// summary:
			//		Removes the Anchor CSS class from a node.
			//		According to `dojo.dnd.Selector`, anchor means that
			//		"an item is selected, and is an anchor for a 'shift' selection".
			//		It's not relevant for Tree at this point, since we don't support multiple selection.
			// tags:
			//		private
			if(this.anchor){
				this._removeItemClass(this.anchor, "Anchor");
				this.anchor = null;
			}
			return this;	// self
		}
});
