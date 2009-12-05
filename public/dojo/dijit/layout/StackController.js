dojo.provide("dijit.layout.StackController");

dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit._Container");
dojo.require("dijit.form.ToggleButton");
dojo.require("dijit.Menu");
dojo.requireLocalization("dijit", "common");

dojo.declare(
		"dijit.layout.StackController",
		[dijit._Widget, dijit._Templated, dijit._Container],
		{
		// summary:
		//		Set of buttons to select a page in a page list.
		// description:
		//		Monitors the specified StackContainer, and whenever a page is
		//		added, deleted, or selected, updates itself accordingly.

			templateString: "<span wairole='tablist' dojoAttachEvent='onkeypress' class='dijitStackController'></span>",

			// containerId: [const] String
			//		The id of the page container that I point to
			containerId: "",

			// buttonWidget: [const] String
			//		The name of the button widget to create to correspond to each page
			buttonWidget: "dijit.layout._StackButton",

			postCreate: function(){
				dijit.setWaiRole(this.domNode, "tablist");

				// TODO: change key from object to id, to get more separation from StackContainer
				this.pane2button = {};		// mapping from panes to buttons
				this.pane2handles = {};		// mapping from panes to dojo.connect() handles
				this.pane2menu = {};		// mapping from panes to close menu

				this._subscriptions=[
					dojo.subscribe(this.containerId+"-startup", this, "onStartup"),
					dojo.subscribe(this.containerId+"-addChild", this, "onAddChild"),
					dojo.subscribe(this.containerId+"-removeChild", this, "onRemoveChild"),
					dojo.subscribe(this.containerId+"-selectChild", this, "onSelectChild"),
					dojo.subscribe(this.containerId+"-containerKeyPress", this, "onContainerKeyPress")
				];
			},

			onStartup: function(/*Object*/ info){
				// summary:
				//		Called after StackContainer has finished initializing
				// tags:
				//		private
				dojo.forEach(info.children, this.onAddChild, this);
				this.onSelectChild(info.selected);
			},

			destroy: function(){
				for(var pane in this.pane2button){
					this.onRemoveChild(pane);
				}
				dojo.forEach(this._subscriptions, dojo.unsubscribe);
				this.inherited(arguments);
			},

			onAddChild: function(/*Widget*/ page, /*Integer?*/ insertIndex){
				// summary:
				//		Called whenever a page is added to the container.
				//		Create button corresponding to the page.
				// tags:
				//		private

				// add a node that will be promoted to the button widget
				var refNode = dojo.doc.createElement("span");
				this.domNode.appendChild(refNode);
				// create an instance of the button widget
				var cls = dojo.getObject(this.buttonWidget);
				var button = new cls({label: page.title, closeButton: page.closable}, refNode);
				this.addChild(button, insertIndex);
				this.pane2button[page] = button;
				page.controlButton = button;	// this value might be overwritten if two tabs point to same container

				var handles = [];
				handles.push(dojo.connect(button, "onClick", dojo.hitch(this,"onButtonClick",page)));
				if(page.closable){
					handles.push(dojo.connect(button, "onClickCloseButton", dojo.hitch(this,"onCloseButtonClick",page)));
					// add context menu onto title button
					var _nlsResources = dojo.i18n.getLocalization("dijit", "common");
					var closeMenu = new dijit.Menu({targetNodeIds:[button.id], id:button.id+"_Menu"});
					var mItem = new dijit.MenuItem({label:_nlsResources.itemClose});
					handles.push(dojo.connect(mItem, "onClick", dojo.hitch(this, "onCloseButtonClick", page)));
					closeMenu.addChild(mItem);
					this.pane2menu[page] = closeMenu;
				}
				this.pane2handles[page] = handles;
				if(!this._currentChild){ // put the first child into the tab order
					button.focusNode.setAttribute("tabIndex", "0");
					this._currentChild = page;
				}
				//make sure all tabs have the same length
				if(!this.isLeftToRight() && dojo.isIE && this._rectifyRtlTabList){
					this._rectifyRtlTabList();
				}
			},

			onRemoveChild: function(/*Widget*/ page){
				// summary:
				//		Called whenever a page is removed from the container.
				//		Remove the button corresponding to the page.
				// tags:
				//		private

				if(this._currentChild === page){ this._currentChild = null; }
				dojo.forEach(this.pane2handles[page], dojo.disconnect);
				delete this.pane2handles[page];
				var menu = this.pane2menu[page];
				if (menu){
					menu.destroyRecursive();
					delete this.pane2menu[page];
				}
				var button = this.pane2button[page];
				if(button){
					// TODO? if current child { reassign }
					button.destroy();
					delete this.pane2button[page];
				}
			},

			onSelectChild: function(/*Widget*/ page){
				// summary:
				//		Called when a page has been selected in the StackContainer, either by me or by another StackController
				// tags:
				//		private

				if(!page){ return; }

				if(this._currentChild){
					var oldButton=this.pane2button[this._currentChild];
					oldButton.attr('checked', false);
					oldButton.focusNode.setAttribute("tabIndex", "-1");
				}

				var newButton=this.pane2button[page];
				newButton.attr('checked', true);
				this._currentChild = page;
				newButton.focusNode.setAttribute("tabIndex", "0");
				var container = dijit.byId(this.containerId);
				dijit.setWaiState(container.containerNode, "labelledby", newButton.id);
			},

			onButtonClick: function(/*Widget*/ page){
				// summary:
				//		Called whenever one of my child buttons is pressed in an attempt to select a page
				// tags:
				//		private

				var container = dijit.byId(this.containerId);	// TODO: do this via topics?
				container.selectChild(page); 
			},

			onCloseButtonClick: function(/*Widget*/ page){
				// summary:
				//		Called whenever one of my child buttons [X] is pressed in an attempt to close a page
				// tags:
				//		private

				var container = dijit.byId(this.containerId);
				container.closeChild(page);
				var b = this.pane2button[this._currentChild];
				if(b){
					dijit.focus(b.focusNode || b.domNode);
				}
			},
			
			// TODO: this is a bit redundant with forward, back api in StackContainer
			adjacent: function(/*Boolean*/ forward){
				// summary:
				//		Helper for onkeypress to find next/previous button
				// tags:
				//		private

				if(!this.isLeftToRight() && (!this.tabPosition || /top|bottom/.test(this.tabPosition))){ forward = !forward; }
				// find currently focused button in children array
				var children = this.getChildren();
				var current = dojo.indexOf(children, this.pane2button[this._currentChild]);
				// pick next button to focus on
				var offset = forward ? 1 : children.length - 1;
				return children[ (current + offset) % children.length ]; // dijit._Widget
			},

			onkeypress: function(/*Event*/ e){
				// summary:
				//		Handle keystrokes on the page list, for advancing to next/previous button
				//		and closing the current page if the page is closable.
				// tags:
				//		private

				if(this.disabled || e.altKey ){ return; }
				var forward = null;
				if(e.ctrlKey || !e._djpage){
					var k = dojo.keys;
					switch(e.charOrCode){
						case k.LEFT_ARROW:
						case k.UP_ARROW:
							if(!e._djpage){ forward = false; }
							break;
						case k.PAGE_UP:
							if(e.ctrlKey){ forward = false; }
							break;
						case k.RIGHT_ARROW:
						case k.DOWN_ARROW:
							if(!e._djpage){ forward = true; }
							break;
						case k.PAGE_DOWN:
							if(e.ctrlKey){ forward = true; }
							break;
						case k.DELETE:
							if(this._currentChild.closable){
								this.onCloseButtonClick(this._currentChild);
							}
							dojo.stopEvent(e);
							break;
						default:
							if(e.ctrlKey){
								if(e.charOrCode === k.TAB){
									this.adjacent(!e.shiftKey).onClick();
									dojo.stopEvent(e);
								}else if(e.charOrCode == "w"){
									if(this._currentChild.closable){
										this.onCloseButtonClick(this._currentChild);
									}
									dojo.stopEvent(e); // avoid browser tab closing.
								}
							}
					}
					// handle page navigation
					if(forward !== null){
						this.adjacent(forward).onClick();
						dojo.stopEvent(e);
					}
				}
			},

			onContainerKeyPress: function(/*Object*/ info){
				// summary:
				//		Called when there was a keypress on the container
				// tags:
				//		private
				info.e._djpage = info.page;
				this.onkeypress(info.e);
			}
	});


dojo.declare("dijit.layout._StackButton",
		dijit.form.ToggleButton,
		{
		// summary:
		//		Internal widget used by StackContainer.
		// description:
		//		The button-like or tab-like object you click to select or delete a page
		// tags:
		//		private

		// Override _FormWidget.tabIndex.
		// StackContainer buttons are not in the tab order by default.
		// TODO: unclear if we need this; doesn't _KeyNavContainer (superclass of StackController) do it for us?
		tabIndex: "-1",
		
		postCreate: function(/*Event*/ evt){
			dijit.setWaiRole((this.focusNode || this.domNode), "tab");
			this.inherited(arguments);
		},
		
		onClick: function(/*Event*/ evt){
			// summary:
			//		This is for TabContainer where the tabs are <span> rather than button,
			//		so need to set focus explicitly (on some browsers)
			//		Note that you shouldn't override this method, but you can connect to it.
			dijit.focus(this.focusNode);

			// ... now let StackController catch the event and tell me what to do
		},

		onClickCloseButton: function(/*Event*/ evt){
			// summary:
			//		StackContainer connects to this function; if your widget contains a close button
			//		then clicking it should call this function.
			//		Note that you shouldn't override this method, but you can connect to it.
			evt.stopPropagation();
		}
	});

