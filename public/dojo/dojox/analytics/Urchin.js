dojo.provide("dojox.analytics.Urchin");

/*=====
dojo.mixin(djConfig,{
	// urchin: String
	//		Used by `dojox.analytics.Urchin` as the default UA-123456-7 account
	//		number used when being created. Alternately, you can pass an acct:"" 
	//		parameter to the constructor a la: new dojox.analytics.Urchin({ acct:"UA-123456-7" });
	urchin: ""
});
=====*/

dojo.declare("dojox.analytics.Urchin", null, {
	// summary: A Google-analytics helper, for post-onLoad inclusion of the tracker, and 
	//		dynamic tracking during long-lived page cycles. 
	//
	// description:
	//		A small class object will allows for lazy-loading the Google Analytics API
	//		at any point during a page lifecycle. Most commonly, Google-Analytics is loaded
	//		via a synchronous script tag in the body, which causes `dojo.addOnLoad` to 
	//		stall until the external API has been completely loaded. The Urchin helper
	//		will load the API on the fly, and provide a convenient API to use, wrapping
	//		Analytics for Ajaxy or single page applications.
	//
	//		The class can be instantiated two ways: Programatically, by passing an
	//		`acct:` parameter, or via Markup / dojoType and defining a djConfig 
	//		parameter `urchin:`
	//
	//		IMPORTANT: 
	//		This module will not work simultaneously with the core dojox.analytics 
	//		package. If you need the ability to run Google Analytics AND your own local
	//		analytics system, you MUST include dojox.analytics._base BEFORE dojox.analytics.Urchin
	//
	//	example:
	//	|	// create the tracker programatically:
	//	|	var tracker = new dojox.analytics.Urchin({ acct:"UA-123456-7" });
	//
	//	example: 
	//	|	// define the urchin djConfig option:
	//	|	var djConfig = { urchin: "UA-123456-7" };
	//	|
	//	|	// and in markup:
	//	|	<div dojoType="dojox.analytics.Urchin"></div>
	//	|	// or code:
	//	|	new dojox.analytics.Urchin();
	//
	//	example:
	//	|	// create and define all analytics with one tag. 
	//	|	<div dojoType="dojox.analytics.Urchin" acct="UA-12345-67"></div>
	//
	// acct: String
	//		your GA urchin tracker account number. Overrides `djConfig.urchin`
	acct: dojo.config.urchin,

	// loadInterval: Integer
	//		Time (in ms) to wait before checking for a ready Analytics API
	loadInterval: 42,
	
	// decay: Float
	// 		Multipler for the interval loadInterval to ensure timer does not
	//		run amok in the event our _gat object is never defined. This 
	//		is multiplied against the last `loadInterval` and added, causing
	//		the interval to continuosly become longer, until a `timeout` 
	//		limit is reached.
	decay: 0.5,
	
	// timeout: Integer
	//		Time (in ms) for the load interval to reach before giving up
	//		all together. Note: this isn't an overall time, this is the
	//		time of the interval being adjusted by the `decay` property.
	timeout: 4200,

	constructor: function(args){
		// summary: initialize this Urchin instance. Immediately starts the load
		//		sequence, so defer construction until (ideally) after onLoad and
		//		potentially widget parsing.
		this.tracker = null;
		dojo.mixin(this, args);
		this._loadGA();
	},
	
	_loadGA: function(){
		// summary: load the ga.js file and begin initialization process
		var gaHost = ("https:" == document.location.protocol) ? "https://ssl." : "http://www.";
		dojo.create('script', {
			src: gaHost + "google-analytics.com/ga.js"
		}, dojo.doc.getElementsByTagName("head")[0]);
		setTimeout(dojo.hitch(this, "_checkGA"), this.loadInterval);
	},

	_checkGA: function(){
		// summary: sniff the global _gat variable Google defines and either check again
		//		or fire onLoad if ready.
		if(this.loadInterval > this.timeout){ return; }
		setTimeout(dojo.hitch(this, !window["_gat"] ? "_checkGA" : "_gotGA"), this.loadInterval);
		this.loadInterval *= (this.decay + 1);
	},

	_gotGA: function(){
		// summary: initialize the tracker
		this.tracker = _gat._getTracker(this.acct);
		this.tracker._initData();
		this.GAonLoad.apply(this, arguments);
	},
	
	GAonLoad: function(){
		// summary: Stub function to fire when urchin is complete
		//	description:
		//		This function is executed when the tracker variable is 
		//		complete and initialized. The initial trackPageView (with
		//		no arguments) is called here as well, so remeber to call 
		//		manually if overloading this method.
		//
		//	example:
		//	Create an Urchin tracker that will track a specific page on init
		//	after page load (or parsing, if parseOnLoad is true)
		//	|	dojo.addOnLoad(function(){
		//	|		new dojox.ananlytics.Urchin({
		//	|			acct:"UA-12345-67", 
		//	|			GAonLoad: function(){
		//	|				this.trackPageView("/custom-page");
		//	|			}
		//	|		});
		//	|	});
		
		this.trackPageView();
	},
	
	trackPageView: function(/* string */url){
		// summary: A public API attached to this widget instance, allowing you 
		//		Ajax-like notification of updates. 
		//
		//	url: String
		//		A location to tell the tracker to track, eg: "/my-ajaxy-endpoint"
		//
		//	example:
		//	Track clicks from a container of anchors and populate a `ContentPane`
		//	|	// 'tracker' is our `Urchin` instance, pane is the `ContentPane` ref.
		//	|	dojo.connect(container, "onclick", function(e){
		//	|		var ref = dojo.attr(e.target, "href");
		//	|		tracker.trackPageView(ref);
		//	|		pane.attr("href", ref); 
		//	|	});
		
		this.tracker._trackPageview.apply(this, arguments);
	}
	
});
