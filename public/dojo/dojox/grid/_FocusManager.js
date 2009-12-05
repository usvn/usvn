dojo.provide("dojox.grid._FocusManager");

dojo.require("dojox.grid.util");

// focus management
dojo.declare("dojox.grid._FocusManager", null, {
	// summary:
	//	Controls grid cell focus. Owned by grid and used internally for focusing.
	//	Note: grid cell actually receives keyboard input only when cell is being edited.
	constructor: function(inGrid){
		this.grid = inGrid;
		this.cell = null;
		this.rowIndex = -1;
		this._connects = [];
		this._connects.push(dojo.connect(this.grid.domNode, "onfocus", this, "doFocus"));
		this._connects.push(dojo.connect(this.grid.domNode, "onblur", this, "doBlur"));
		this._connects.push(dojo.connect(this.grid.lastFocusNode, "onfocus", this, "doLastNodeFocus"));
		this._connects.push(dojo.connect(this.grid.lastFocusNode, "onblur", this, "doLastNodeBlur"));
		this._connects.push(dojo.connect(this.grid,"_onFetchComplete", this, "_delayedCellFocus"));
		this._connects.push(dojo.connect(this.grid,"postrender", this, "_delayedHeaderFocus"));
	},
	destroy: function(){
		dojo.forEach(this._connects, dojo.disconnect);
		delete this.grid;
		delete this.cell;
	},
	_colHeadNode: null,
	_colHeadFocusIdx: null,
	tabbingOut: false,
	focusClass: "dojoxGridCellFocus",
	focusView: null,
	initFocusView: function(){
		this.focusView = this.grid.views.getFirstScrollingView() || this.focusView;
		this._initColumnHeaders();
	},
	isFocusCell: function(inCell, inRowIndex){
		// summary:
		//	states if the given cell is focused
		// inCell: object
		//	grid cell object
		// inRowIndex: int
		//	grid row index
		// returns:
		//	true of the given grid cell is focused
		return (this.cell == inCell) && (this.rowIndex == inRowIndex);
	},
	isLastFocusCell: function(){
		if(this.cell){
			return (this.rowIndex == this.grid.rowCount-1) && (this.cell.index == this.grid.layout.cellCount-1);
		}
		return false;
	},
	isFirstFocusCell: function(){
		if(this.cell){
			return (this.rowIndex == 0) && (this.cell.index == 0);
		}
		return false;
	},
	isNoFocusCell: function(){
		return (this.rowIndex < 0) || !this.cell;
	},
	isNavHeader: function(){
		// summary:
		//	states whether currently navigating among column headers.
		// returns:
		//	true if focus is on a column header; false otherwise. 
		return (!!this._colHeadNode);
	},
	getHeaderIndex: function(){
		// summary:
		//	if one of the column headers currently has focus, return its index.
		// returns:
		//	index of the focused column header, or -1 if none have focus.
		if(this._colHeadNode){
			return dojo.indexOf(this._findHeaderCells(), this._colHeadNode);
		}else{
			return -1;
		}
	},
	_focusifyCellNode: function(inBork){
		var n = this.cell && this.cell.getNode(this.rowIndex);
		if(n){
			dojo.toggleClass(n, this.focusClass, inBork);
			if(inBork){
				var sl = this.scrollIntoView();
				try{
					if(!this.grid.edit.isEditing()){
						dojox.grid.util.fire(n, "focus");
						if(sl){ this.cell.view.scrollboxNode.scrollLeft = sl; }
					}
				}catch(e){}
			}
		}
	},
	_delayedCellFocus: function(){
		if(this.isNavHeader()){
				return;
		}
		var n = this.cell && this.cell.getNode(this.rowIndex);
		if(n){ 
			try{
				if(!this.grid.edit.isEditing()){
					dojo.toggleClass(n, this.focusClass, true);
					dojox.grid.util.fire(n, "focus");
				}
			} 
			catch(e){}
		}
	},
	_delayedHeaderFocus: function(){
		if(this.isNavHeader()){
			this.focusHeader();
			//this may need clickSelect?
		}
	},
	_initColumnHeaders: function(){
		this._connects.push(dojo.connect(this.grid.viewsHeaderNode, "onblur", this, "doBlurHeader"));
		var headers = this._findHeaderCells();
		for(var i = 0; i < headers.length; i++){
			this._connects.push(dojo.connect(headers[i], "onfocus", this, "doColHeaderFocus"));
			this._connects.push(dojo.connect(headers[i], "onblur", this, "doColHeaderBlur"));
		}
	},
	_findHeaderCells: function(){
		// This should be a one liner:
		//	dojo.query("th[tabindex=-1]", this.grid.viewsHeaderNode);
		// But there is a bug in dojo.query() for IE -- see trac #7037.
		var allHeads = dojo.query("th", this.grid.viewsHeaderNode);
		var headers = [];
		for (var i = 0; i < allHeads.length; i++){
			var aHead = allHeads[i];
			var hasTabIdx = dojo.hasAttr(aHead, "tabindex");
			var tabindex = dojo.attr(aHead, "tabindex");
			if (hasTabIdx && tabindex < 0) {
				headers.push(aHead);
			}
		}
		return headers;
	},
	scrollIntoView: function(){
		var info = (this.cell ? this._scrollInfo(this.cell) : null);
		if(!info || !info.s){
			return null;
		}
		var rt = this.grid.scroller.findScrollTop(this.rowIndex);
		// place cell within horizontal view
		if(info.n && info.sr){
			if(info.n.offsetLeft + info.n.offsetWidth > info.sr.l + info.sr.w){
				info.s.scrollLeft = info.n.offsetLeft + info.n.offsetWidth - info.sr.w;
			}else if(info.n.offsetLeft < info.sr.l){
				info.s.scrollLeft = info.n.offsetLeft;
			}
		}
		// place cell within vertical view
		if(info.r && info.sr){
			if(rt + info.r.offsetHeight > info.sr.t + info.sr.h){
				this.grid.setScrollTop(rt + info.r.offsetHeight - info.sr.h);
			}else if(rt < info.sr.t){
				this.grid.setScrollTop(rt);
			}
		}

		return info.s.scrollLeft;
	},
	_scrollInfo: function(cell, domNode){
		if(cell){
			var cl = cell,
				sbn = cl.view.scrollboxNode,
				sbnr = {
					w: sbn.clientWidth,
					l: sbn.scrollLeft,
					t: sbn.scrollTop,
					h: sbn.clientHeight
				},
				rn = cl.view.getRowNode(this.rowIndex);
			return {
				c: cl,
				s: sbn,
				sr: sbnr,
				n: (domNode ? domNode : cell.getNode(this.rowIndex)),
				r: rn
			};
		}
		return null;
	},
	_scrollHeader: function(currentIdx){
		var info = null;
		if(this._colHeadNode){
			var cell = this.grid.getCell(currentIdx);
			info = this._scrollInfo(cell, cell.getNode(0));
		}
		if(info && info.s && info.sr && info.n){
			// scroll horizontally as needed.
			var scroll = info.sr.l + info.sr.w;
			if(info.n.offsetLeft + info.n.offsetWidth > scroll){
				info.s.scrollLeft = info.n.offsetLeft + info.n.offsetWidth - info.sr.w;
			}else if(info.n.offsetLeft < info.sr.l){
				info.s.scrollLeft = info.n.offsetLeft;
			}else if(dojo.isIE <= 7 && cell && cell.view.headerNode){
				// Trac 7158: scroll dojoxGridHeader for IE7 and lower
				cell.view.headerNode.scrollLeft = info.s.scrollLeft;
			}
		}
	},
	styleRow: function(inRow){
		return;
	},
	setFocusIndex: function(inRowIndex, inCellIndex){
		// summary:
		//	focuses the given grid cell
		// inRowIndex: int
		//	grid row index
		// inCellIndex: int
		//	grid cell index
		this.setFocusCell(this.grid.getCell(inCellIndex), inRowIndex);
	},
	setFocusCell: function(inCell, inRowIndex){
		// summary:
		//	focuses the given grid cell
		// inCell: object
		//	grid cell object
		// inRowIndex: int
		//	grid row index
		if(inCell && !this.isFocusCell(inCell, inRowIndex)){
			this.tabbingOut = false;
			this._colHeadNode = this._colHeadFocusIdx = null;
			this.focusGridView();
			this._focusifyCellNode(false);
			this.cell = inCell;
			this.rowIndex = inRowIndex;
			this._focusifyCellNode(true);
		}
		// even if this cell isFocusCell, the document focus may need to be rejiggered
		// call opera on delay to prevent keypress from altering focus
		if(dojo.isOpera){
			setTimeout(dojo.hitch(this.grid, 'onCellFocus', this.cell, this.rowIndex), 1);
		}else{
			this.grid.onCellFocus(this.cell, this.rowIndex);
		}
	},
	next: function(){
		// summary:
		//	focus next grid cell
		if(this.cell){
			var row=this.rowIndex, col=this.cell.index+1, cc=this.grid.layout.cellCount-1, rc=this.grid.rowCount-1;
			if(col > cc){
				col = 0;
				row++;
			}
			if(row > rc){
				col = cc;
				row = rc;
			}
			if(this.grid.edit.isEditing()){ //when editing, only navigate to editable cells
				var nextCell = this.grid.getCell(col);
				if (!this.isLastFocusCell() && !nextCell.editable){
					this.cell=nextCell;
					this.rowIndex=row;
					this.next();
					return;
				}
			}
			this.setFocusIndex(row, col);
		}
	},
	previous: function(){
		// summary:
		//	focus previous grid cell
		if(this.cell){
			var row=(this.rowIndex || 0), col=(this.cell.index || 0) - 1;
			if(col < 0){
				col = this.grid.layout.cellCount-1;
				row--;
			}
			if(row < 0){
				row = 0;
				col = 0;
			}
			if(this.grid.edit.isEditing()){ //when editing, only navigate to editable cells
				var prevCell = this.grid.getCell(col);
				if (!this.isFirstFocusCell() && !prevCell.editable){
					this.cell=prevCell;
					this.rowIndex=row;
					this.previous();
					return;
				}
			}
			this.setFocusIndex(row, col);
		}
	},
	move: function(inRowDelta, inColDelta) {
		// summary:
		//	focus grid cell or column header based on position relative to current focus
		// inRowDelta: int
		// vertical distance from current focus
		// inColDelta: int
		// horizontal distance from current focus

		// Handle column headers.
		if(this.isNavHeader()){
			var headers = this._findHeaderCells();
			var currentIdx = dojo.indexOf(headers, this._colHeadNode);
			currentIdx += inColDelta;
			if((currentIdx >= 0) && (currentIdx < headers.length)){
				this._colHeadNode = headers[currentIdx];
				this._colHeadFocusIdx = currentIdx;
				this._scrollHeader(currentIdx);
				this._colHeadNode.focus();
			}
		}else{
			if(this.cell){
				// Handle grid proper.
				var sc = this.grid.scroller,
					r = this.rowIndex,
					rc = this.grid.rowCount-1,
					row = Math.min(rc, Math.max(0, r+inRowDelta));
				if(inRowDelta){
					if(inRowDelta>0){
						if(row > sc.getLastPageRow(sc.page)){
							//need to load additional data, let scroller do that
							this.grid.setScrollTop(this.grid.scrollTop+sc.findScrollTop(row)-sc.findScrollTop(r));
						}
					}else if(inRowDelta<0){
						if(row <= sc.getPageRow(sc.page)){
							//need to load additional data, let scroller do that
							this.grid.setScrollTop(this.grid.scrollTop-sc.findScrollTop(r)-sc.findScrollTop(row));
						}
					}
				}
				var cc = this.grid.layout.cellCount-1,
					i = this.cell.index,
					col = Math.min(cc, Math.max(0, i+inColDelta));
				this.setFocusIndex(row, col);
				if(inRowDelta){
					this.grid.updateRow(r);
				}
			}
		}
	},
	previousKey: function(e){
		if(this.grid.edit.isEditing()){
			dojo.stopEvent(e);
			this.previous();
		}else if(!this.isNavHeader()){
			this.focusHeader();
			dojo.stopEvent(e);
		}else{
			this.tabOut(this.grid.domNode);
		}
	},
	nextKey: function(e) {
		var isEmpty = this.grid.rowCount == 0;
		if(e.target === this.grid.domNode){
			this.focusHeader();
			dojo.stopEvent(e);
		}else if(this.isNavHeader()){
			// if tabbing from col header, then go to grid proper. If grid is empty this.grid.rowCount == 0
			this._colHeadNode = this._colHeadFocusIdx= null;
			if(this.isNoFocusCell() && !isEmpty){
				this.setFocusIndex(0, 0);
			}else if(this.cell && !isEmpty){
				if(this.focusView && !this.focusView.rowNodes[this.rowIndex]){
				// if rowNode for current index is undefined (likely as a result of a sort and because of #7304) 
				// scroll to that row
					this.grid.scrollToRow(this.rowIndex);
				}
				this.focusGrid();
			}else{
				this.tabOut(this.grid.lastFocusNode);
			}
		}else if(this.grid.edit.isEditing()){
			dojo.stopEvent(e);
			this.next();
		}else{
			this.tabOut(this.grid.lastFocusNode);
		}
	},
	tabOut: function(inFocusNode){
		this.tabbingOut = true;
		inFocusNode.focus();
	},
	focusGridView: function(){
		dojox.grid.util.fire(this.focusView, "focus");
	},
	focusGrid: function(inSkipFocusCell){
		this.focusGridView();
		this._focusifyCellNode(true);
	},
	focusHeader: function(){
		var headerNodes = this._findHeaderCells();

		if (!this._colHeadFocusIdx) {
			if (this.isNoFocusCell()) {
				this._colHeadFocusIdx = 0;
			}
			else {
				this._colHeadFocusIdx = this.cell.index;
			}
		}
		this._colHeadNode = headerNodes[this._colHeadFocusIdx];
		if(this._colHeadNode){
			dojox.grid.util.fire(this._colHeadNode, "focus");
			this._focusifyCellNode(false);
		}
	},
	doFocus: function(e){
		// trap focus only for grid dom node
		if(e && e.target != e.currentTarget){
			dojo.stopEvent(e);
			return;
		}
		// do not focus for scrolling if grid is about to blur
		if(!this.tabbingOut){
			this.focusHeader();
		}
		this.tabbingOut = false;
		dojo.stopEvent(e);
	},
	doBlur: function(e){
		dojo.stopEvent(e);	// FF2
	},
	doBlurHeader: function(e){
		dojo.stopEvent(e);	// FF2
	},
	doLastNodeFocus: function(e){
		if (this.tabbingOut){
			this._focusifyCellNode(false);
		}else if(this.grid.rowCount >0){
			if (this.isNoFocusCell()){
				this.setFocusIndex(0,0);
			}
			this._focusifyCellNode(true);
		}else {
			this.focusHeader();
		}
		this.tabbingOut = false;
		dojo.stopEvent(e);	 // FF2
	},
	doLastNodeBlur: function(e){
		dojo.stopEvent(e);	 // FF2
	},
	doColHeaderFocus: function(e){
		dojo.toggleClass(e.target, this.focusClass, true);
		this._scrollHeader(this.getHeaderIndex());
	},
	doColHeaderBlur: function(e){
		dojo.toggleClass(e.target, this.focusClass, false);
	}		
});
