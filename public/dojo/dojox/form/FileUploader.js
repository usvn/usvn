dojo.provide("dojox.form.FileUploader");
dojo.require("dojox.embed.Flash");
dojo.require("dojo.io.iframe");
dojo.require("dojox.html.styles");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojox.embed.flashVars");
dojo.experimental("dojox.form.FileUploader");
	//	Usage Notes:
	//		To center text vertically, use vertical-align:middle;
	//			which emulates a boxModel button. Using line-height to center text
	//			can cause height problems in IE6
	
	
(function(){
	
	var swfPath = dojo.config.uploaderPath || dojo.moduleUrl("dojox.form", "resources/uploader.swf");
	
	var mixin = function(o1,o2){
		// custom mixin
		var o = {}, nm;
		for(nm in o1){
			if(dojo.isObject(o1[nm])){
				o[nm] = mixin({}, o1[nm]);
			}else{
				o[nm] = o1[nm];	
			}
			
		}
		for(nm in o2){
			if(dojo.isObject(o2[nm])){
				if(dojo.isObject(o[nm])){
					mixin(o[nm], o2[nm]);
				}else{
					mixin({}, o2[nm]);
				}
			}else{
				o[nm] = o2[nm];
			}
		}
		return o;
	};
	
	var urlencode = function(url){
		// Using symbols in place of URL chars that will break in Flash serialization.
		if(!url || url == "none"){
			return false;
		}
		return url.replace(/:/g,"||").replace(/\./g,"^^").replace("url(", "").replace(")","").replace(/'/g,"").replace(/"/g,"");
	};
	
	var isButton = function(node){
		// testing if button for styling purposes
		var tn = node.tagName.toLowerCase();
		return tn == "button" || tn == "input";
	};
	
	var getTextStyle = function(node){
		// getting font info
		var o = {};
		o.ff = dojo.style(node, "fontFamily");
		o.ff = o.ff.replace(/\"|\'/g, "");
		o.fw = dojo.style(node, "fontWeight");
		o.fi = dojo.style(node, "fontStyle");
		o.fs = parseInt(dojo.style(node, "fontSize"), 10);
		
		o.fc = new dojo.Color(dojo.style(node, "color")).toHex();
		o.fc = parseInt(o.fc.substring(1,Infinity),16);
		
		o.lh = dojo.style(node, "lineHeight");
		o.ta = dojo.style(node, "textAlign");
		o.ta = o.ta == "start" || !o.ta ? "left" : o.ta;
		o.va = isButton(node) ? "middle" : o.lh == o.h ? "middle" : dojo.style(node, "verticalAlign");
		return o;
	};
	
	var getText = function(node){
		// Get the text of the button. It's possible to use HTML in the Flash Button,
		//	but the results are not spectacular.
		var cn = dojo.trim(node.innerHTML);
		if(cn.indexOf("<")>-1){
			cn = escape(cn);
		}
		return cn;
	};
	
	var getStyle = function(node){
		// getting the style of a node. Using very abbreviated characters which the
		//	Flash movie understands.
		var o = {};
		var dim = dojo.contentBox(node);
		
		var pad = dojo._getPadExtents(node);
		o.p = [pad.t, pad.w-pad.l, pad.h-pad.t, pad.l];
		o.w = dim.w + pad.w;
		o.h = dim.h + pad.h;
		o.d = dojo.style(node, "display");
		var clr = new dojo.Color(dojo.style(node, "backgroundColor"));
		// if no color, Safari sets #000000 and alpha=0
		// 	since we don't support alpha, it makes black
		//	- make it white
		o.bc = clr.a == 0 ? "#ffffff" : clr.toHex();
		o.bc = parseInt(o.bc.substring(1,Infinity),16);
		var url = urlencode(dojo.style(node, "backgroundImage"));
		if(url){
			o.bi = {
				url:url,
				rp:dojo.style(node, "backgroundRepeat"),
				pos: escape(dojo.style(node, "backgroundPosition"))
			};
			if(!o.bi.pos){
				// IE does Xpx and Ypx, not "X% Y%"
				var rx = dojo.style(node, "backgroundPositionX");
				var ry = dojo.style(node, "backgroundPositionY");
				rx = (rx == "left") ? "0%" : (rx == "right") ? "100%" : rx;
				ry = (ry == "top") ? "0%" : (ry == "bottom") ? "100%" : ry;
				o.bi.pos = escape(rx+" "+ry);
			}
		}
		return mixin(o, getTextStyle(node));
	};
	var getTempNodeStyle = function(node, _class, isDijitButton){
		// This sets up a temp node to get the style of the hover, active, and disabled states
		var temp, style;
		if(isDijitButton){
			// backwards compat until dojo 1.5
			temp = dojo.place("<"+node.tagName+"><span>"+node.innerHTML+" "+_class+"</span></"+node.tagName+">", node.parentNode);
			var first = temp.firstChild;
			dojo.addClass(first, node.className);
			dojo.addClass(temp, _class);
			style = getStyle(first);
		}else{
			temp = dojo.place("<"+node.tagName+">"+node.innerHTML+"</"+node.tagName+">", node.parentNode);
			dojo.addClass(temp, node.className);
			dojo.addClass(temp, _class);
			temp.id = node.id;
			style = getStyle(temp);
		}
		dojo.destroy(temp);
		return style;
	};
	
	var isUpperCase = function(ltr){
		// returns true if character is upper case
		return ltr.charCodeAt(0) < 91;
	};
	
	dojo.declare("dojox.form.FileUploader", [dijit._Widget, dijit._Templated], {
		// version:
		//		1.4
		// summary: 
		// 		Handles File Uploading to a server (PHP script included for testing)
		//
		//		***NEW: FileUploader is now a WIDGET. You do not have to pass a button
		//		in. Passing a button is still supported until version 1.5 to maintain
		//		backwards compatibility, but it is not reccomended. Just create your
		//		uploader like any other widget.
		//
		// description:
		//		If the correct version of Flash Player is available (> 9.0) , a SWF
		//		is used. If Flash Player is not installed or is outdated, a typical
		//		html fileInput is used. This process can be overridden with
		//		force:"flash" or force:"html".
		//
		//		FileUploader works with Flash 10.
		//
		//		***NEW: The button styles are now recreated in Flash, so there is no longer
		//		using an invisible Flash movie with wmode=transparent. This way the Flash button
		//		is actually placed inline with the DOM, not floating above it and contstantly
		//		resetting its position. The "Windows Firefox clickable bug" should be fixed (and
		//		hopefully some Linux problems).
		//
		//		***NEW: The HTML button is created in a new way and it is now inline as is the
		//		FLash button. Styling is much easier and more versatile.
		//
		//	Dependencies:
		//		FileUploader no longer uses FileInput.css. It now uses FileUploader.css
		//		See requires for JavaScript dependencies.
		//
		//	NEW FEATURES - 
		//		There are a ton of features and fixes in this version.
		//			Disabled: Can be toggled with widget.attr("disable", true|false)
		//			Submit: A convenience method has been added for if the uploader is in a form.
		//					Instead of submitting the form, call uploader.submit(theForm), and the
		//					Uploader will handle all of the form values and post the data.
		//			Selected List: If passing the ID of a container, the Uploaders will populate it
		//					with the selected files.
		//			Deleting Files: You can now delete pending files.
		//			Progress Built in: showProgress:true will change the button to a progress
		//					bar on upload.
		//			Progress Attach: Passing progressWidgetId will tell the Uploader of a progress
		//					widget. If the Progress widget is initially hidden, it will change to
		//					visible and then restored after upload.
		//			A11Y: The Flash button can be accessed with the TAB key. (The HTML cannot due
		//					to browser limtations)
		//			Deferred Uploading: (Flash only) throttles the upload to one file at a time
		//
		// 	UploadFile.php fixes:
		//		HTML didn't send postdata properly
		//		removed unecessary code, clarified things
		//		html files return sizes on upload 	
		//
		//	CDN USERS - 
		//		FileUpload now works with the CDN but with limitations. The SWF must 
		//		be from the same domain as the HTML page. 'swfPath' has been exposed
		//		so that you may link to that file (could of course be the same SWF in 
		//		dojox resource folder). The SWF will *NOT* work from the
		//		CDN server. This would require a special XML file that would allow 
		//		access to your server, and the logistics to that is impossible.
		//
		//	OPERA USERS -
		//		It works better than the 1.3 version. fileInputs apperantly can't have opacity
		//		set to zero. The Flash uploader works but files are auto-uploaded. Must be a
		//		flashVar problem.
		//
		
		uploadUrl: "",
		
	/*=====
		//
		//	button: dijit.form.Button or a domNode
		// 		DEPRECATED: The "fake" button that when clicked, launches the upload dialog
		//
		//	isDebug: Boolean
		//		If true, outputs traces from the SWF to console. What exactly gets passed
		//		is very relative, and depends upon what traces have been left in the DEFT SWF.
		isDebug:false,
		//
		//	devMode: Boolean.
		//		REMOVED - no longer useful with new code design.
		//
		//	id: String
		//		The object id, just like any other widget in Dojo. However, this id
		//		is also used as a reference for the SWF
		//
		//	hoverClass: String
		//		The name of the class that will style the button in a "hover" state. A specific
		//		class should be made to do this. Do not rely on a target like button:hover{...}
		hoverClass:"",
		//
		//	activeClass: String
		//		The name of the class that will style the button in a "press" state. A specific
		//		class should be made to do this. Do not rely on a target like button:active{...}
		activeClass:"",
		//
		//	disabledClass: String
		//		The name of the class that will style the button when its disabled.
		disabledClass:"",
		//
		//	force: String
		//		Use "flash" to always use Flash (and hopefully force the user to download the plugin
		//		if they don't have it). Use "html" to always use the HTML uploader. An empty string
		//		(default) will check for the right version of Flash and use HTML if not available.
		force:"",
		//
		//	uploaderType: [readonly] String
		//		Internal. What type of uploader is being used: "flash" or "html"
		uploaderType:"",
		//
		//	flashObject: [readonly] dojox.embed.Flash 
		//		The object that creates the SWF embed object. Mostly Internal.
		flashObject: null,
		//
		//	flashMovie: [readonly] Function
		//		The SWF. Mostly Internal.
		flashMovie: null,
		//
		//	flashDiv: [readonly] HTMLNode
		//		DEPRECATED for insideNode
		//		The div that holds the SWF and form/fileInput
		flashDiv: null,
		//
		//	insideNode: [readonly] HTMLNode
		//		The div that holds the SWF and form/fileInput
		insideNode: null,
		//
		//	deferredUploading: Boolean
		//		(Flash only) throttles the upload to one file at a time
		deferredUploading:false,
		//
		//	fileListId: String
		//		The id of a dom node to be used as a container for the pending file list.
		fileListId:"",
	=====*/
		//
		//	uploadOnChange: Boolean
		//		If true, uploads imediately after a file has been selected. If false,
		//		waits for upload() to be called.
		uploadOnChange: false,
		
		//	selectMultipleFiles: Boolean
		//		If true and flash mode, multiple files may be selected from the dialog.
		//		If html mode, files are not uploaded until upload() is called. The references
		//		to each file is incremented:uploadedfile0, uploadedfile1, uploadedfile2... etc.
		selectMultipleFiles: true,
		//
		//	htmlFieldName: String
		//		The name of the field of the fileInput that the server is expecting
		htmlFieldName:"uploadedfile",
		//
		//	flashFieldName: String
		//		The name of the field of the flash uploaded files that the server is expecting
		flashFieldName:"flashUploadFiles",
		//
		// fileMask:  Array[ Array[Description, FileTypes], Array[...]...] 
		// 		(an array, or an array of arrays)
		//		Restrict file selection to certain file types
		// 		Empty array defaults to "All Files"
		// example:
		//	fileMask = ["Images", "*.jpg;*.jpeg;*.gif;*.png"]
		//	or
		//	fileMask = [
		//		["Jpeg File", 	"*.jpg;*.jpeg"],
		//		["GIF File", 	"*.gif"],
		//		["PNG File", 	"*.png"],
		//		["All Images", 	"*.jpg;*.jpeg;*.gif;*.png"],
		//	]
		//	NOTE: MacType is not supported, as it does not work very well.
		//			fileMask will work on a Mac, but differently than 
		//			Windows.
		fileMask: [],
		//
		//	minFlashVersion: Number
		//		The minum of version of Flash player to target. 0 would always install Flash, 100
		//		would never install it. The Flash Player has supported multiple uploads since
		//		version 8, so it could go as low as that safely.
		minFlashVersion:9,
		//
		//	tabIndex: Number|String
		//		The tab order in the DOM
		tabIndex:-1,
		//
		//	showProgress: Boolean
		//		If true, the button changes to a progress bar during upload.
		showProgress:false,
		//
		//	progressMessage: String
		//		The message shown while the button is changed to a progress bar
		progressMessage:"Loading",
		//
		//	progressBackgroundUrl: String|Uri
		//		The background image to use for the button-progress
		progressBackgroundUrl:dojo.moduleUrl("dijit", "themes/tundra/images/buttonActive.png"),
		//
		//	progressBackgroundColor: String|Number
		//		The background color to use for the button-progress
		progressBackgroundColor:"#ededed",
		//
		//	progressWidgetId:String
		//		The widget id of a Dijit Progress bar. The Uploader will bind to it and update it
		//		automatically.
		progressWidgetId:"",
		
		templateString:'<div><div dojoAttachPoint="progNode"><div dojoAttachPoint="progTextNode"></div></div><div dojoAttachPoint="insideNode"></div></div>',
		
		log: function(){
			//	summary:
			//		Due to the excessive logging necessary to make this code happen,
			//		It's easier to turn it on and off here in one place.
			//		Also helpful if there are multiple uploaders on one page.
			if(this.isDebug){
				console.log.apply(console, arguments);
			}
		},
		
		postMixInProperties: function(){
			// private stuff:
			this.fileList = [];
			this._subs = [];
			this._cons = [];
			this.fileInputs = [];
			this.fileCount = 0;
			this.flashReady = false;
			this._disabled = false;
			this.uploaderType = ((dojox.embed.Flash.available >= this.minFlashVersion || this.force=="flash") && this.force != "html") ? "flash" : "html";
			
			if(!this.swfPath){
				this.swfPath = swfPath;
			}
			this.getButtonStyle();
		},
		
		postCreate: function(){
			this.setButtonStyle();
			if(this.uploaderType == "flash"){
				this.uploaderType = "flash";
				this.createFlashUploader();
			}else{
				this.uploaderType = "html";
				this.createHtmlUploader();
			}
			
			if(this.fileListId){
				dojo.connect(dojo.byId(this.fileListId), "click", this, function(evt){
					var p = evt.target.parentNode.parentNode.parentNode; // in a table
					if(p.id && p.id.indexOf("file_")>-1){
						this.removeFile(p.id.split("file_")[1]);
					}
				});
			}
			
	//		this._animateProgress();
		},
		
		getButtonStyle: function(){
			// summary:
			//		Internal.
			//		Get necessary style information from srcRefNode and
			//		assigned styles
			//
			if(!this.srcNodeRef && this.button && this.button.domNode){
				// backwards compat for a Dijit button
				this.isDijitButton = true;
				var cls = this.button.domNode.className + " dijitButtonNode";
				var txt = getText(dojo.query(".dijitButtonText", this.button.domNode)[0]);
				var domTxt = '<button id="'+this.button.id+'" class="'+cls+'">'+txt+'</button>';
				this.srcNodeRef = dojo.place(domTxt, this.button.domNode, "after");	
				this.button.destroy();
				
				this.hoverClass = "dijitButtonHover";
				this.pressClass = "dijitButtonActive";
				this.disabledClass = "dijitButtonDisabled";
			}
			
			this.norm = getStyle(this.srcNodeRef);
			this.width = this.norm.w;
			this.height = this.norm.h;
			
			if(this.uploaderType == "flash"){
				if(this.hoverClass){
					this.over = getTempNodeStyle(this.srcNodeRef, this.hoverClass, this.isDijitButton);
				}else{
					this.over = mixin({}, this.norm);
				}
				
				if(this.activeClass){
					this.down = getTempNodeStyle(this.srcNodeRef, this.activeClass, this.isDijitButton);				
				}else{
					this.down = mixin({}, this.norm);
				}
				
				if(this.disabledClass){
					this.dsbl = getTempNodeStyle(this.srcNodeRef, this.disabledClass, this.isDijitButton);				
				}else{
					this.dsbl = mixin({}, this.norm);
				}
				
				this.fhtml = {
					cn:getText(this.srcNodeRef),
					nr:this.norm,
					ov:this.over,
					dn:this.down,
					ds:this.dsbl
				};
			}else{
				this.fhtml = {
					cn:getText(this.srcNodeRef),
					nr:this.norm
				}
			}
			
		},
		
		setButtonStyle: function(){
			// summary:
			//		Internal.
			//		Set up internal dom nodes
			//
			dojo.style(this.domNode, {
				width:this.fhtml.nr.w+"px",
				height:(this.fhtml.nr.h)+"px",
				padding:"0px",
				lineHeight:"normal",
				position:"relative"
			});
			if(this.showProgress){
				this.progTextNode.innerHTML = this.progressMessage;
				dojo.style(this.progTextNode, {
					width:this.fhtml.nr.w+"px",
					height:(this.fhtml.nr.h+0)+"px",
					padding:"0px",
					margin:"0px",
					left:"0px",
					lineHeight:(this.fhtml.nr.h+0)+"px",
					position:"absolute"
				});
				dojo.style(this.progNode, {
					width:this.fhtml.nr.w+"px",
					height:(this.fhtml.nr.h+0)+"px",
					padding:"0px",
					margin:"0px",
					left:"0px",
					position:"absolute",
					display:"none",
					backgroundImage:"url("+this.progressBackgroundUrl+")",
					backgroundPosition:"bottom",
					backgroundRepeat:"repeat-x",
					backgroundColor:this.progressBackgroundColor
				});
			}
			dojo.style(this.insideNode,{
				position:"absolute",
				top:"0px",
				left:"0px",
				display:""
			});
			dojo.addClass(this.domNode, this.srcNodeRef.className);
			if(this.fhtml.nr.d.indexOf("inline")>-1){
				dojo.addClass(this.domNode, "dijitInline");
			}
				
			try{
				this.insideNode.innerHTML = this.fhtml.cn;
			}catch(e){
				// You have got to be kidding me. IE does us he favor of checking that
				//	we aren't inserting the improper type of content with innerHTML into
				//	an inline element. Alert us with an "Unknown Runtime Error". You can't
				//	MAKE this stuff up.
				//
				console.warn("IE inline node", this.domNode.outerHTML)
				if(this.uploaderType == "flash"){
				this.insideNode = this.insideNode.parentNode.removeChild(this.insideNode);
					dojo.body().appendChild(this.insideNode);
					this.insideNode.innerHTML = this.fhtml.cn;
					var c = dojo.connect(this, "onReady", this, function(){ dojo.disconnect(c);
						this.insideNode = this.insideNode.parentNode.removeChild(this.insideNode);
						this.domNode.appendChild(this.insideNode);
					});
				}else{
					this.insideNode.appendChild(document.createTextNode(this.fhtml.cn));
				}
			}
			
			
			this.flashDiv = this.insideNode; //backwards compat - rem in 1.5
		},
		
		
		/*************************
		 *	   Public Events	 *
		 *************************/
		
		// The following events are inherited from _Widget and still may be connected:
		// onClick
		// onMouseUp
		// onMouseDown
		// onMouseOver
		// onMouseOut
		
		onChange: function(dataArray){
			//	summary:
			// 		stub to connect 
			// 		Fires when files are selected
			// 		Event is an array of last files selected
		},
		
		onProgress: function(dataArray){
			// summary:
			// 		Stub to connect 
			// 		Fires as progress returns from SWF
			// 		Event is an array of all files uploading
			//		Can be connected to for HTML uploader,
			//		but will not return anything.
		},
		
		onComplete: function(dataArray){
			// summary
			// stub to connect 
			// Fires when all files have uploaded
			// Event is an array of all files
		},
		
		onCancel: function(){
			// summary:
			// 		Stub to connect 
			// 		Fires when dialog box has been closed 
			//		without a file selection
			this.log("Upload Canceled");
		},
		
		onError: function(evtObject){
			//FIXME: Unsure of a standard form for receiving errors
			var type = evtObject.type ? evtObject.type.toUpperCase() : "ERROR";
			var msg = evtObject.msg ? evtObject.msg : evtObject;
			console.error("FLASH/ERROR/"+type , msg);
		},
		
		onReady: function(){
			// summary:
			//	Stub - when uploader is finished building
		},
		/*************************
		 *	   Public Methods	 *
		 *************************/
		submit: function(/* form node ? */form){
			// summary:
			//		If FileUploader is in a form, and other data should be sent
			//		along with the files, use this instead of form submit.
			//
			var data = form ? dojo.formToObject(form) : null;
			this.upload(data);
			return false; // Boolean
		},
		upload: function(/*Object ? */data){
			// summary:
			// 		When called, begins file upload
			//	data: Object
			//		postData to be sent to server
			if(!this.fileList.length){
				return false;
			}
			if(!this.uploadUrl){
				console.warn("uploadUrl not provided. Aborting.");
				return false;
			}
			if(!this.showProgress){
				this.attr("disabled", true);
			}else{
				//this._progInterval = setTimeout(dojo.hitch(this, "animateLoad"),800);
			}
			
			if(this.progressWidgetId){
				
				var node = dijit.byId(this.progressWidgetId).domNode;
				console.warn("PROGRESS BAR", node, dojo.style(node, "display"));
				if(dojo.style(node, "display") == "none"){
					this.restoreProgDisplay = "none";
					dojo.style(node, "display", "block");
				}
				if(dojo.style(node, "visibility") == "hidden"){
					this.restoreProgDisplay = "hidden";
					dojo.style(node, "visibility", "visible");
				}
			}
			
			if(data){
				this.postData = data;
			}
			this.log("upload type:", this.uploaderType, " - postData:", this.postData);
			
			for (var i=0;i<this.fileList.length;i++){
				var f = this.fileList[i];
				f.bytesLoaded = 0;
				f.bytesTotal = f.size || 100000;
				f.percent = 0;
			}
			if(this.uploaderType == "flash"){
				this.uploadFlash();
			}else{
				this.uploadHTML();
			}
			// prevent form submit
			return false;
		},
		removeFile: function(/*String*/name, /*Boolean*/noListEdit){
			// summary:
			//		Removes a file from the pending file list.
			//		Removes pending data from teh Flash movie
			//		and fileInputes from teh HTML uploader.
			//		If a file container node is bound, the file
			//		will also be removed.
			// name:String
			//		The name of the file to be removed. Typically the file name,
			//		such as: picture01.png
			// noListEdit:Boolean
			//		Internal. If true don't remove files from list.
			//
			var i;
			for(i=0;i<this.fileList.length;i++){
				if(this.fileList[i].name == name){
					if(!noListEdit){ // if onComplete, don't do this
						this.fileList.splice(i,1);
					}
					break;
				}
			}
			if(this.uploaderType == "flash"){
				this.flashMovie.removeFile(name);
			}else if(!noListEdit){
				dojo.destroy(this.fileInputs[i]);
				this.fileInputs.splice(i,1);
			}
			if(this.fileListId){
				dojo.destroy("file_"+name);	
			}
		},
		destroyAll: function(){
			//	summary:
			// 		Destroys button
			console.warn("DEPRECATED for 1.5 - use destroy() instead");
			this.destroy();
		},
		
		destroy: function(){
			//	summary:
			//		Destroys uploader button
			if(this.uploaderType == "flash" && !this.flashMovie){
				this._cons.push(dojo.connect(this, "onLoad", this, "destroy"));
				return;
			}
			dojo.forEach(this._subs, function(s){
				dojo.unsubscribe(s);
			});
			dojo.forEach(this._cons, function(c){
				dojo.disconnect(c);
			});
			if(this.scrollConnect){
				dojo.disconnect(this.scrollConnect);
			}
			if(this.uploaderType == "flash"){
				this.flashObject.destroy();
				dojo.destroy(this.flashDiv);
			}
			this.inherited(arguments);
		},
		hide: function(){
			//	summary:
			//		Hides the upload button.
			console.warn("DEPRECATED for 1.5 - use dojo.style(domNode, 'display', 'none' instead");
			dojo.style(this.domNode, 'display', 'none');
		},
		
		show: function(){
			//	summary:
			//		Shows the upload button. This is called
			//		when within a dialog.
			console.warn("DEPRECATED for 1.5 - use dojo.style(domNode, 'display', '') instead");
			dojo.style(this.domNode, 'display', '');
		},
		disable: function(/*Boolean*/disabled){
			console.warn("DEPRECATED: FileUploader.disable() - will be removed in 1.5. Use attr('disable', true) instead.")
			this.attr("disable", disabled);
		},
		/*************************
		 *	   Private Events	 *
		 *************************/
		_displayProgress: function(/*Boolean or Number */display){
			// summary:
			//		Shows and updates the built-in progress bar.
			//
			if(display === true){
				if(this.uploaderType == "flash"){
					dojo.style(this.insideNode,"left", "-1000px");
				}else{
					dojo.style(this.insideNode,"display", "none");	
				}
				dojo.style(this.progNode,"display","");
			}else if(display === false){
				dojo.style(this.insideNode,"display", "");
				dojo.style(this.insideNode,"left", "0px");
				dojo.style(this.progNode,"display","none");
			}else{
				var w = display * this.fhtml.nr.w;
				dojo.style(this.progNode,{
					width:w+"px"
				});
			}
		},
		_animateProgress: function(){
			// summary:
			//		Internal. Animated the built-in progress bar
			this._displayProgress(true);
			var _uploadDone = false;
			var c = dojo.connect(this, "_complete", function(){
				dojo.disconnect(c);
				_uploadDone = true;
			});
			var w = 0;
			var interval = setInterval(dojo.hitch(this, function(){
				w+=5;
				if(w>this.fhtml.nr.w){
					w = 0;
					_uploadDone = true;
				}
				this._displayProgress(w/this.fhtml.nr.w);
				
				if(_uploadDone){
					clearInterval(interval);
					setTimeout(dojo.hitch(this, function(){
						this._displayProgress(false);
					}), 500);
				}
				
			}),50);
		},
		
		_error: function(evt){
			this.onError(evt);
		},
		
		_addToFileList: function(){
			// summary:
			//		Internal only. If there is a file list, adds a file to it.
			//		If you need to use a function such as this, connect to
			//		onChange and update ourtside of this widget.
			//
			if(this.fileListId){
				var str = '';
				dojo.forEach(this.fileList, function(d){
					// have to use tables because of IE. Grumble.
					str += '<table id="file_'+d.name+'" class="fileToUpload"><tr><td class="fileToUploadClose"></td><td class="fileToUploadName">'+d.name+'</td><td class="fileToUploadSize">'+Math.ceil(d.size*.001)+'kb</td></tr></table>'	
				}, this);
				dojo.byId(this.fileListId).innerHTML = str;
			}
		},
		
		_change: function(dataArray){
			// summary:
			//		Internal. Updates uploader selection
			if(dojo.isIE){
				//IE6 uses the entire path in the name, which isn't terrible, but much different
				// than everything else
				dojo.forEach(dataArray, function(f){
					f.name = f.name.split("\\")[f.name.split("\\").length-1];
				});
			}
			if(this.selectMultipleFiles){
				this.fileList = this.fileList.concat(dataArray);
			}else{
				if(this.fileList[0]){
					this.removeFile(this.fileList[0].name);
				}
				this.fileList = dataArray;
			}
			this._addToFileList();
			this.onChange(dataArray);
			if(this.uploadOnChange){
				this.upload();
			}else if(this.uploaderType == "html" && this.selectMultipleFiles){
				this._buildFileInput();
				this._connectInput();
			}
		},
		
		_complete: function(dataArray){
			// summary:
			//		Internal. Handles tasks after files have finished uploading
			//
			dataArray = dojo.isArray(dataArray) ? dataArray : [dataArray];
			
			// Yes. Yes I do have to do three loops here. ugh.
			//
			// Check if one of the files had an error
			dojo.forEach(dataArray, function(f){
				if(f.ERROR){
					console.error(f.ERROR);
					this._error(new Error(f.ERROR));	
				}
			}, this);
			
			// Have to be set them all too 100%, because
			// onProgress does not always fire
			dojo.forEach(this.fileList, function(f){
				f.bytesLoaded = 1;
				f.bytesTotal = 1;
				f.percent = 100;
				this._progress(f);
			}, this);
			// we're done. remove files.
			dojo.forEach(this.fileList, function(f){
				this.removeFile(f.name, true);
			}, this);
			
			this.onComplete(dataArray);
			
			this.fileList = [];
			this._resetHTML();
			this.attr("disabled", false);
			
			
			if(this.restoreProgDisplay){
				// using timeout so prog shows on screen for at least a short time
				setTimeout(dojo.hitch(this, function(){
					dojo.style(dijit.byId(this.progressWidgetId).domNode,
						this.restoreProgDisplay == "none" ? "display" : "visibility",
						this.restoreProgDisplay
					);
				}), 700);
			}
			
		},
		
		_progress: function(dataObject){
			// summary:
			//		Internal. Calculate progress
			var total = 0;
			var loaded = 0;
			for (var i = 0; i < this.fileList.length; i++){
				var f = this.fileList[i];
				if(f.name == dataObject.name){
					f.bytesLoaded = dataObject.bytesLoaded;
					f.bytesTotal = dataObject.bytesTotal;
					f.percent = Math.ceil(f.bytesLoaded / f.bytesTotal * 100);
					console.info(f.name, "percent:", f.percent)
				} 
				loaded += Math.ceil(.001 * f.bytesLoaded);
				total += Math.ceil(.001 * f.bytesTotal);
			}
			var percent = Math.ceil(loaded / total * 100);
			if(this.progressWidgetId){
				dijit.byId(this.progressWidgetId).update({progress:percent+"%"});
			}
			if(this.showProgress){
				this._displayProgress(percent * .01);
			}
			this.onProgress(this.fileList);
			
		},
		_getDisabledAttr: function(){
			// summary:
			//		Internal. To get disabled use: widget.attr("disabled");
			return this._disabled;
		},
		
		_setDisabledAttr: function(disabled){
			// summary:
			//		Internal. To set disabled use: widget.attr("disabled", true | false);
			if(this._disabled == disabled){ return; }
			
			if(this.uploaderType == "flash"){
				if(!this.flashReady){
					var _fc = dojo.connect(this, "onReady", this, function(){
						dojo.disconnect(_fc);
						this._setDisabledAttr(disabled);
					});
					return;
				}
				this._disabled = disabled;
				this.flashMovie.doDisable(disabled);
				if(disabled){
					dojo.addClass(this.domNode, this.disabledClass);
				}else{
					dojo.removeClass(this.domNode, this.disabledClass);
				}
			}else{
				this._disabled = disabled;
				if(disabled){
					dojo.addClass(this.domNode, this.disabledClass);
					dojo.style(this._fileInput, "display", "none");
				}else{
					dojo.removeClass(this.domNode, this.disabledClass);
					dojo.style(this._fileInput, "display", "");
				}
			}
		},
		_onFlashBlur: function(){
			// summary:
			//		Internal. Detects when Flash movies reliquishes focus.
			//		We have to find all teh tabIndexes in the doc and figure
			//		out whom to gove focus to next.
			this.flashMovie.blur();
			if(!this.nextFocusObject && this.tabIndex){
				var nodes = dojo.query("[tabIndex]");
				for(var i=0;i<nodes.length;i++){
					if(nodes[i].tabIndex >= Number(this.tabIndex)+1){
						this.nextFocusObject = nodes[i];
						break;
					}
				}
			}
			this.nextFocusObject.focus();
		},
		_disconnect: function(){
			// summary:
			//		Internal. Disconnects fileInput in favor of new one.
			dojo.forEach(this._cons, function(c){
				dojo.disconnect(c);
			});
		},
		
		/*************************
		 *			HTML		 *
		 *************************/
		uploadHTML: function(){
			// summary:
			//		Internal. You could use this, but you should use upload() or submit();
			//		which can also handle the post data.
			//
			// NOTE on deferredUploading:
			// This is not enabled for HTML. Workaround would be to force
			//	singleFile uploads.
			//	TODO:
			//	Investigate removing fileInputs and resending form
			//	multiple times adding each fileInput
			//
			dojo.destroy(this._fileInput);
			this._setHtmlPostData();
			if(this.showProgress){
				this._animateProgress();
			}
			dojo.io.iframe.send({
				url: this.uploadUrl,
				form: this._formNode,
				handleAs: "json",
				handle: dojo.hitch(this, function(data, ioArgs, widgetRef){
					this._complete(data);
				})
			}); 
		},
		createHtmlUploader: function(){
			// summary:
			//		Internal. Fires of methods to build HTML Uploader.
			this._buildForm();
			this._setFormStyle();
			this._buildFileInput();
			this._connectInput();
			this._styleContent();
			this.onReady();
		},
		_connectInput: function(){
			// summary:
			//		Internal. HTML Uploader connections. These get disconnected
			//		after upload or if multi upload.
			this._disconnect();
			this._cons.push(dojo.connect(this._fileInput, "mouseover", this, function(evt){
				dojo.addClass(this.domNode, this.hoverClass);
				this.onMouseOver(evt);
			}));
			this._cons.push(dojo.connect(this._fileInput, "mouseout", this, function(evt){
				dojo.removeClass(this.domNode, this.activeClass);
				dojo.removeClass(this.domNode, this.hoverClass);
				this.onMouseOut(evt);
				this._checkHtmlCancel("off");
			}));
			this._cons.push(dojo.connect(this._fileInput, "mousedown", this, function(evt){
				dojo.addClass(this.domNode, this.activeClass);
				dojo.removeClass(this.domNode, this.hoverClass);
				this.onMouseDown(evt);
			}));
			this._cons.push(dojo.connect(this._fileInput, "mouseup", this, function(evt){
				dojo.removeClass(this.domNode, this.activeClass);
				this.onMouseUp(evt);
				this.onClick(evt);
				this._checkHtmlCancel("up");
			}));
			
			this._cons.push(dojo.connect(this._fileInput, "change", this, function(){
				this.log("html change");
				this._checkHtmlCancel("change");
				this._change([{
					name: this._fileInput.value,
					type: "",
					size: 0
				}]);
			}));
			if(this.tabIndex>=0){
				dojo.attr(this.domNode, "tabIndex", this.tabIndex);
			}
		},
		_checkHtmlCancel: function(mouseType){
			// summary:
			//		Internal. Check if the dialog was opened and canceled without file selection.
			if(mouseType == "change"){
				this.dialogIsOpen = false;
			}
			if(mouseType == "up"){
				this.dialogIsOpen = true;
			}
			if(mouseType == "off"){
				if(this.dialogIsOpen){
					this.onCancel();
				}
				this.dialogIsOpen = false;
			}
		},
		_styleContent: function(){
			// summary:
			//		Internal.Apply style to node
			var o = this.fhtml.nr;
			dojo.style(this.insideNode, {
				width:o.w+"px",
				height:o.va == "middle"?o.h+"px":"auto",
				lineHeight:o.va == "middle"?o.h+"px":"auto",
				textAlign:o.ta,
				paddingTop:o.p[0]+"px",
				paddingRight:o.p[1]+"px",
				paddingBottom:o.p[2]+"px",
				paddingLeft:o.p[3]+"px"
			});
		},
		_resetHTML: function(){
			// summary:
			//		Internal. After upload, this is called to clear the form and build a new
			//		fileInput.
			if(this.uploaderType == "html" && this._formNode){
				dojo.query("*", this._formNode).forEach(function(n){
					dojo.destroy(n);
				});
				this.fileCount = 0;
				this._buildFileInput();
				this._connectInput();
			}
		},
		_buildForm: function(){
			// summary:
			//		Build the form that holds the fileInput
			//
			if(this._formNode){ return; }
			
			if(dojo.isIE){
				this._formNode = document.createElement('<form enctype="multipart/form-data" method="post">');
				this._formNode.encoding = "multipart/form-data";
			}else{
				this._formNode = document.createElement('form');
				this._formNode.setAttribute("enctype", "multipart/form-data");
			}
			this._formNode.id = dijit.getUniqueId("FileUploaderForm"); // needed for dynamic style
			this.domNode.appendChild(this._formNode);
		},
		
		_buildFileInput: function(){
			// summary:
			//		Build the fileInput field
			//
			if(this._fileInput){
				this._disconnect();
				// FIXME:
				//	Just hiding it which works, but we lose
				//	reference to it and can't remove it from
				//	the upload list.
				this._fileInput.id = this._fileInput.id + this.fileCount;
				dojo.style(this._fileInput, "display", "none");
			}
			this._fileInput = document.createElement('input');
			this.fileInputs.push(this._fileInput);
			// server will need to know this variable:
			var nm = this.htmlFieldName;
			var _id = this.id;
			if(this.selectMultipleFiles){
				nm += this.fileCount;
				_id += this.fileCount;
				this.fileCount++;
			}
			
			dojo.attr(this._fileInput, {
				id:this.id,
				name:nm,
				type:"file"
			});
			
			dojo.addClass(this._fileInput, "dijitFileInputReal");
			this._formNode.appendChild(this._fileInput);
			var real = dojo.marginBox(this._fileInput);
			dojo.style(this._fileInput, {
				position:"relative",
				left:(this.fhtml.nr.w - real.w) + "px",
				opacity:0
			});
		},
		_setFormStyle: function(){
			// summary:
			//		Apply a dynamic style to the form and input
			var size = Math.max(2, Math.max(Math.ceil(this.fhtml.nr.w / 60), Math.ceil(this.fhtml.nr.h / 15)));
			// Now create a style associated with the form ID
			dojox.html.insertCssRule("#" + this._formNode.id + " input", "font-size:" + size + "em");
			dojo.style(this.domNode, {
				overflow:"hidden",
				position:"relative"
			});
			dojo.style(this.insideNode, "position", "absolute");
		},
		_setHtmlPostData: function(){
			// summary:
			//		Internal.Apply postData to hidden fields in form
			if(this.postData){
				for (var nm in this.postData){
					var f = document.createElement('input');
					dojo.attr(f, "type", "hidden");
					dojo.attr(f, "name", nm);
					dojo.attr(f, "value", this.postData[nm]);
					this._formNode.appendChild(f);
				}
			}
		},
		
		/*************************
		 *			FLASH		 *
		 *************************/
		uploadFlash: function(){
			// summary:
			//		Internal. You should use upload() or submit();
			try{
				if(this.showProgress){
					this._displayProgress(true);
					var c = dojo.connect(this, "_complete", this, function(){
						dojo.disconnect(c);
						this._displayProgress(false);
					});
				}
				this.flashMovie.doUpload(this.postData);
				
			}catch(err){
				throw new Error("Sorry, the SWF failed to initialize." + err);
			}
		},
		createFlashUploader: function(){
			// summary:
			//		Internal. Creates Flash Uploader
			this.uploadUrl = this.uploadUrl.toString();
			if(this.uploadUrl){
				if(this.uploadUrl.toLowerCase().indexOf("http")<0 && this.uploadUrl.indexOf("/")!=0){
					// Appears to be a relative path. Attempt to 
					//	convert it to absolute, so it will better 
					//target the SWF.
					//
					var loc = window.location.href.split("/");
					loc.pop();
					loc = loc.join("/")+"/";
					this.uploadUrl = loc+this.uploadUrl;
					this.log("SWF Fixed - Relative loc:", loc, " abs loc:", this.uploadUrl);
				}
			}else{
				//console.warn("Warning: no uploadUrl provided.");
			}
			var w = this.fhtml.nr.w;
			var h = this.fhtml.nr.h;
			
			var args = {
				expressInstall:true,
				path: this.swfPath.uri || this.swfPath,
				width: w,
				height: h,
				allowScriptAccess:"always",
				allowNetworking:"all",
				vars: {
					uploadDataFieldName: this.flashFieldName,
					uploadUrl: this.uploadUrl,
					uploadOnSelect: this.uploadOnChange,
					deferredUploading:this.deferredUploading,
					selectMultipleFiles: this.selectMultipleFiles,
					id: this.id,
					isDebug: this.isDebug,
					devMode:this.devMode,
					flashButton:dojox.embed.flashVars.serialize("fh", this.fhtml),
					fileMask:dojox.embed.flashVars.serialize("fm", this.fileMask)
				},
				params: {
					scale:"noscale"
				}
				
			};
			
			this.flashObject = new dojox.embed.Flash(args, this.insideNode);
			console.log("start flash...")
			this.flashObject.onError = function(msg){
				console.warn("Flash Error:", msg);
			};
			this.flashObject.onReady = dojo.hitch(this, function(){
				//console.log("READY");
			});
			this.flashObject.onLoad = dojo.hitch(this, function(mov){
				//console.log("ONLOAD")
				this.flashMovie = mov;
				this.flashReady = true;
				this.onReady();
			});
			this._connectFlash();
		},
		_connectFlash: function(){
			// 	summary:
			//		Subscribing to published topics coming from the
			//		Flash uploader.
			// 	description:
			//		Sacrificing some readbilty for compactness. this.id
			//		will be on the beginning of the topic, so more than
			//		one uploader can be on a page and can have unique calls.
			//
			this._doSub("/filesSelected", "_change");
			this._doSub("/filesUploaded", "_complete");
			this._doSub("/filesProgress", "_progress");
			this._doSub("/filesError", "_error");
			this._doSub("/filesCanceled", "onCancel");
			this._doSub("/stageBlur", "_onFlashBlur");
			
			this._doSub("/up", "onMouseUp");
			this._doSub("/down", "onMouseDown");
			this._doSub("/over", "onMouseOver");
			this._doSub("/out", "onMouseOut");
			
			this.connect(this.domNode, "focus", function(){
				// TODO: some kind of indicator that the Flash button
				//	is in focus
				this.flashMovie.focus();
				this.flashMovie.doFocus();
				dojo.connect(document, "keydown", function(evt){
					console.log(evt.keyCode)
				});
			});
			if(this.tabIndex>=0){
				dojo.attr(this.domNode, "tabIndex", this.tabIndex);		
			}
		},
		
		_doSub: function(subStr, funcStr){
			// summary:
			//		Internal. Shortcut for subscribes to Flash movie
			this._subs.push(dojo.subscribe(this.id + subStr, this, funcStr));
		}
		
	});
	
})();
