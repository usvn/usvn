dojo.provide("dijit._tree.dndSource");

dojo.require("dijit._tree.dndSelector");
dojo.require("dojo.dnd.Manager");

/*=====
dijit._tree.__SourceArgs = function(){
	// summary:
	//		A dict of parameters for Tree source configuration.
	// isSource: Boolean?
	//		Can be used as a DnD source. Defaults to true.
	// accept: String[]
	//		List of accepted types (text strings) for a target; defaults to
	//		["text"]
	// copyOnly: Boolean?
	//		Copy items, if true, use a state of Ctrl key otherwise,
	// dragThreshold: Number
	//		The move delay in pixels before detecting a drag; 0 by default
	// betweenThreshold: Integer
	//		Distance from upper/lower edge of node to allow drop to reorder nodes
	this.isSource = isSource;
	this.accept = accept;
	this.autoSync = autoSync;
	this.copyOnly = copyOnly;
	this.dragThreshold = dragThreshold;
	this.betweenThreshold = betweenThreshold;
}
=====*/

dojo.declare("dijit._tree.dndSource", dijit._tree.dndSelector, {
	// summary:
	//		Handles drag and drop operations (as a source or a target) for `dijit.Tree`
	
	// isSource: [private] Boolean
	//		Can be used as a DnD source.
	isSource: true,

	// accept: String[]
	//		List of accepted types (text strings) for the Tree; defaults to
	//		["text"]
	accept: ["text"],

	// copyOnly: [private] Boolean
	//		Copy items, if true, use a state of Ctrl key otherwise
	copyOnly: false,

	// dragThreshold: Number
	//		The move delay in pixels before detecting a drag; 0 by default
	dragThreshold: 0,

	// betweenThreshold: Integer
	//		Distance from upper/lower edge of node to allow drop to reorder nodes
	betweenThreshold: 0,

	// skipForm: [private] Boolean
	//		TODO: apparently unused, should be removed
	skipForm: false,

	constructor: function(/*dijit.Tree*/ tree, /*dijit._tree.__SourceArgs*/ params){
		// summary:
		//		a constructor of the Tree DnD Source
		// tags:
		//		private
		if(!params){ params = {}; }
		dojo.mixin(this, params);
		this.isSource = typeof params.isSource == "undefined" ? true : params.isSource;
		var type = params.accept instanceof Array ? params.accept : ["text"];
		this.accept = null;
		if(type.length){
			this.accept = {};
			for(var i = 0; i < type.length; ++i){
				this.accept[type[i]] = 1;
			}
		}

		// class-specific variables
		this.isDragging = false;
		this.mouseDown = false;
		this.targetAnchor = null;	// DOMNode corresponding to the currently moused over TreeNode
		this.targetBox = null;	// coordinates of this.targetAnchor
		this.dropPosition = "";	// whether mouse is over/after/before this.targetAnchor
		this._lastX = 0;
		this._lastY = 0;

		// states
		this.sourceState  = "";
		if(this.isSource){
			dojo.addClass(this.node, "dojoDndSource");
		}
		this.targetState  = "";
		if(this.accept){
			dojo.addClass(this.node, "dojoDndTarget");
		}

		// set up events
		this.topics = [
			dojo.subscribe("/dnd/source/over", this, "onDndSourceOver"),
			dojo.subscribe("/dnd/start",  this, "onDndStart"),
			dojo.subscribe("/dnd/drop",   this, "onDndDrop"),
			dojo.subscribe("/dnd/cancel", this, "onDndCancel")
		];
		this.events.push(
			// monitor mouse movements to tell when drag starts etc.
			dojo.connect(this.node, "onmousemove", this, "onMouseMove")
		);

	},

	startup: function(){
		// summary:
		//		Apparently unused.  TODO: remove
		// tags:
		//		private
	},
	
	// methods
	checkAcceptance: function(source, nodes){
		// summary:
		//		Checks if the target can accept nodes from this source
		// source: dijit.tree._dndSource
		//		The source which provides items
		// nodes: DOMNode[]
		//		Array of DOM nodes corresponding to nodes being dropped, dijitTreeRow nodes if
		//		source is a dijit.Tree.
		// tags:
		//		extension
		return true;	// Boolean
	},

	copyState: function(keyPressed){
		// summary:
		//		Returns true, if we need to copy items, false to move.
		//		It is separated to be overwritten dynamically, if needed.
		// keyPressed: Boolean
		//		The "copy" control key was pressed
		// tags:
		//		protected
		return this.copyOnly || keyPressed;	// Boolean
	},
	destroy: function(){
		// summary:
		//		Prepares the object to be garbage-collected.
		this.inherited("destroy",arguments);
		dojo.forEach(this.topics, dojo.unsubscribe);
		this.targetAnchor = null;
	},

	_onDragMouse: function(e){
		// summary:
		//		Helper method for processing onmousemove/onmouseover events while drag is in progress.
		//		Keeps track of current drop target.

		var m = dojo.dnd.manager(),
			oldTarget = this.targetAnchor,			// the DOMNode corresponding to TreeNode mouse was previously over
			newTarget = this.current,				// DOMNode corresponding to TreeNode mouse is currently over
			newTargetWidget = this.currentWidget,	// the TreeNode itself
			oldDropPosition = this.dropPosition;	// the previous drop position (over/before/after)

		// calculate if user is indicating to drop the dragged node before, after, or over
		// (i.e., to become a child of) the target node
		var newDropPosition = "Over";
		if(newTarget && this.betweenThreshold > 0){
			// If mouse is over a new TreeNode, then get new TreeNode's position and size
			if(!this.targetBox || oldTarget != newTarget){
				this.targetBox = {
					xy: dojo.coords(newTarget, true),
					w: newTarget.offsetWidth,
					h: newTarget.offsetHeight
				};
			}
			if((e.pageY - this.targetBox.xy.y) <= this.betweenThreshold){
				newDropPosition = "Before";
			}else if((e.pageY - this.targetBox.xy.y) >= (this.targetBox.h - this.betweenThreshold)){
				newDropPosition = "After";
			}
		}
		
		if(newTarget != oldTarget || newDropPosition != oldDropPosition){
			if(oldTarget){
				this._removeItemClass(oldTarget, oldDropPosition);
			}
			if(newTarget){
				this._addItemClass(newTarget, newDropPosition);
			}

			// Check if it's ok to drop the dragged node on/before/after the target node.
			if(!newTarget){
				m.canDrop(false);
			}else if(newTargetWidget == this.tree.rootNode && newDropPosition != "Over"){
				// Can't drop before or after tree's root node; the dropped node would just disappear (at least visually)
				m.canDrop(false);
			}else if(m.source == this && (newTarget.id in this.selection)){
				// Guard against dropping onto yourself (TODO: guard against dropping onto your descendant, #7140)
				m.canDrop(false);
			}else if(this.checkItemAcceptance(newTarget, m.source, newDropPosition.toLowerCase())){
				m.canDrop(true);
			}else{
				m.canDrop(false);
			}

			this.targetAnchor = newTarget;
			this.dropPosition = newDropPosition;
		}
	},

	onMouseMove: function(e){
		// summary:
		//		Called for any onmousemove events over the Tree
		// e: Event
		//		onmousemouse event
		// tags:
		//		private
		if(this.isDragging && this.targetState == "Disabled"){ return; }
		var m = dojo.dnd.manager();
		if(this.isDragging){
			if(this.betweenThreshold > 0){
				this._onDragMouse(e);
			}
		}else{
			if(this.mouseDown && this.isSource &&
			   (Math.abs(e.pageX-this._lastX)>=this.dragThreshold || Math.abs(e.pageY-this._lastY)>=this.dragThreshold)){
				var n = this.getSelectedNodes();
				var nodes=[];
				for (var i in n){
					nodes.push(n[i]);
				}
				if(nodes.length){
					m.startDrag(this, nodes, this.copyState(dojo.dnd.getCopyKeyState(e)));
				}
			}
		}
	},

	onMouseDown: function(e){
		// summary:
		//		Event processor for onmousedown
		// e: Event
		//		onmousedown event
		// tags:
		//		private
		this.mouseDown = true;
		this.mouseButton = e.button;
		this._lastX = e.pageX;
		this._lastY = e.pageY;
		this.inherited("onMouseDown",arguments);
	},

	onMouseUp: function(e){
		// summary:
		//		Event processor for onmouseup
		// e: Event
		//		onmouseup event
		// tags:
		//		private
		if(this.mouseDown){
			this.mouseDown = false;
			this.inherited("onMouseUp",arguments);
		}
	},

	onMouseOver: function(/*TreeNode*/ widget, /*Event*/ e){
		// summary:
		//		Event processor for when mouse is moved over a TreeNode
		// tags:
		//		private

		this.inherited(arguments);

		if(this.isDragging){
			this._onDragMouse(e);
		}
	},

	onMouseOut: function(){
		// summary:
		//		Event processor for when mouse is moved away from a TreeNode
		// tags:
		//		private
		this.inherited(arguments);
		this._unmarkTargetAnchor();
	},

	checkItemAcceptance: function(target, source, position){
		// summary:
		//		Stub function to be overridden if one wants to check for the ability to drop at the node/item level
		// description:
		//		In the base case, this is called to check if target can become a child of source.
		//		When betweenThreshold is set, position="before" or "after" means that we
		//		are asking if the source node can be dropped before/after the target node.
		// target: DOMNode
		//		The dijitTreeRoot DOM node inside of the TreeNode that we are dropping on to
		//		Use dijit.getEnclosingWidget(target) to get the TreeNode.
		// source: dijit._tree.dndSource
		//		The (set of) nodes we are dropping
		// position: String
		//		"over", "before", or "after"
		// tags:
		//		extension
		return true;	
	},
	
	// topic event processors
	onDndSourceOver: function(source){
		// summary:
		//		Topic event processor for /dnd/source/over, called when detected a current source.
		// source: Object
		//		The dijit.tree._dndSource / dojo.dnd.Source which has the mouse over it
		// tags:
		//		private
		if(this != source){
			this.mouseDown = false;
			this._unmarkTargetAnchor();
		}else if(this.isDragging){
			var m = dojo.dnd.manager();
			m.canDrop(false);
		}
	},
	onDndStart: function(source, nodes, copy){
		// summary:
		//		Topic event processor for /dnd/start, called to initiate the DnD operation
		// source: Object
		//		The dijit.tree._dndSource / dojo.dnd.Source which is providing the items
		// nodes: DomNode[]
		//		The list of transferred items, dndTreeNode nodes if dragging from a Tree
		// copy: Boolean
		//		Copy items, if true, move items otherwise
		// tags:
		//		private

		if(this.isSource){
			this._changeState("Source", this == source ? (copy ? "Copied" : "Moved") : "");
		}
		var accepted = this.checkAcceptance(source, nodes);

		this._changeState("Target", accepted ? "" : "Disabled");

		if(accepted){
			dojo.dnd.manager().overSource(this);
		}

		this.isDragging = true;
	},

	itemCreator: function(nodes){
		// summary:
		//		Returns the "item" passed to the drop target
		//		(which is not necessarily a Tree, could be anything)
		// tags:
		//		protected
		return dojo.map(nodes, function(node){
			return {
				"id": node.id,
				"name": node.textContent || node.innerText || ""
			};
		});
	},

	onDndDrop: function(source, nodes, copy){
		// summary:
		//		Topic event processor for /dnd/drop, called to finish the DnD operation.
		// description:
		//		Updates data store items according to where node was dragged from and dropped
		//		to.   The tree will then respond to those data store updates and redraw itself.
		// source: Object
		//		The dijit.tree._dndSource / dojo.dnd.Source which is providing the items
		// nodes: DomNode[]
		//		The list of transferred items, dndTreeNode nodes if dragging from a Tree
		// copy: Boolean
		//		Copy items, if true, move items otherwise
		// tags:
		//		protected
		if(this.containerState == "Over"){
			var tree = this.tree,
				model = tree.model,
				target = this.targetAnchor,
				requeryRoot = false;	// set to true iff top level items change

			this.isDragging = false;

			// Compute the new parent item
			var targetWidget = dijit.getEnclosingWidget(target);
			var newParentItem;
			var insertIndex;
			newParentItem = (targetWidget && targetWidget.item) || tree.item;
			if(this.dropPosition == "Before" || this.dropPosition == "After"){
				// TODO: if there is no parent item then disallow the drop.
				// Actually this should be checked during onMouseMove too, to make the drag icon red.
				newParentItem = (targetWidget.getParent() && targetWidget.getParent().item) || tree.item;
				// Compute the insert index for reordering
				insertIndex = targetWidget.getIndexInParent();
				if(this.dropPosition == "After"){
					insertIndex = targetWidget.getIndexInParent() + 1;
				}
			}else{
				newParentItem = (targetWidget && targetWidget.item) || tree.item;
			}

			// If we are dragging from another source (or at least, another source
			// that points to a different data store), then we need to make new data
			// store items for each element in nodes[].  This call get the parameters
			// to pass to store.newItem()
			var newItemsParams;
			if(source != this){
				newItemsParams = this.itemCreator(nodes, target);
			}

			dojo.forEach(nodes, function(node, idx){
				if(source == this){
					// This is a node from my own tree, and we are moving it, not copying.
					// Remove item from old parent's children attribute.
					// TODO: dijit._tree.dndSelector should implement deleteSelectedNodes()
					// and this code should go there.
					var childTreeNode = dijit.getEnclosingWidget(node),
						childItem = childTreeNode.item,
						oldParentItem = childTreeNode.getParent().item;

					if(typeof insertIndex == "number"){
						if(newParentItem == oldParentItem && childTreeNode.getIndexInParent() < insertIndex){
							insertIndex -= 1;
						}
					}
					model.pasteItem(childItem, oldParentItem, newParentItem, copy, insertIndex);
				}else{
					model.newItem(newItemsParams[idx], newParentItem);
				}
			}, this);

			// Expand the target node (if it's currently collapsed) so the user can see
			// where their node was dropped.   In particular since that node is still selected.
			this.tree._expandNode(targetWidget);
		}
		this.onDndCancel();
	},
	onDndCancel: function(){
		// summary:
		//		Topic event processor for /dnd/cancel, called to cancel the DnD operation
		// tags:
		//		private
		this._unmarkTargetAnchor();
		this.isDragging = false;
		this.mouseDown = false;
		delete this.mouseButton;
		this._changeState("Source", "");
		this._changeState("Target", "");
	},
	
	// When focus moves in/out of the entire Tree
	onOverEvent: function(){
		// summary:
		//		This method is called when mouse is moved over our container (like onmouseenter)
		// tags:
		//		private
		this.inherited(arguments);
		dojo.dnd.manager().overSource(this);
	},
	onOutEvent: function(){
		// summary:
		//		This method is called when mouse is moved out of our container (like onmouseleave)
		// tags:
		//		private
		this._unmarkTargetAnchor();
		var m = dojo.dnd.manager();
		if(this.isDragging){
			m.canDrop(false);
		}
		m.outSource(this);

		this.inherited(arguments);
	},

	_unmarkTargetAnchor: function(){
		// summary:
		//		Removes hover class of the current target anchor
		// tags:
		//		private
		if(!this.targetAnchor){ return; }
		this._removeItemClass(this.targetAnchor, this.dropPosition);
		this.targetAnchor = null;
		this.targetBox = null;
		this.dropPosition = null;
	},

	_markDndStatus: function(copy){
		// summary:
		//		Changes source's state based on "copy" status
		this._changeState("Source", copy ? "Copied" : "Moved");
	}
});

dojo.declare("dijit._tree.dndTarget", dijit._tree.dndSource, {
	// summary:
	//		A Target object, which can be used as a DnD target
	// TODO: Remove.   dijit._tree.dndSource can also be used as a DnD target,
	// so this class doesn't seem useful.
	
	constructor: function(node, params){
		// summary:
		//		A constructor of the Target --- see the Source constructor for details
		this.isSource = false;
		dojo.removeClass(this.node, "dojoDndSource");
	}
});
