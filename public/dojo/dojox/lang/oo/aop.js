dojo.provide("dojox.lang.oo.aop");

dojo.require("dojox.lang.oo.Decorator");
dojo.require("dojox.lang.oo.chain");
dojo.require("dojox.lang.oo.general");

(function(){
	var oo = dojox.lang.oo, md = oo.makeDecorator, ooa = oo.aop;

	// five decorators implementing light-weight AOP weaving

	/*=====
	ooa.before = md(function(name, newValue, oldValue){
		// summary: creates a "before" advise, by calling new function
		// before the old one

		// dummy body
	});

	ooa.around = md(function(name, newValue, oldValue){
		// summary: creates an "around" advise,
		// the previous value is passed as a first argument and can be null,
		// arguments are passed as a second argument

		// dummy body
	});
	=====*/

	// reuse existing decorators
	ooa.before = oo.chain.before;
	ooa.around = oo.general.wrap;

	ooa.afterReturning = md(function(name, newValue, oldValue){
		// summary: creates an "afterReturning" advise,
		// the returned value is passed as the only argument
		return dojo.isFunction(oldValue) ?
			function(){
				var ret = oldValue.apply(this, arguments);
				newValue.call(this, ret);
				return ret;
			} : function(){ newValue.call(this); };
	});

	ooa.afterThrowing = md(function(name, newValue, oldValue){
		// summary: creates an "afterThrowing" advise,
		// the exception is passed as the only argument
		return dojo.isFunction(oldValue) ?
			function(){
				var ret;
				try{
					ret = oldValue.apply(this, arguments);
				}catch(e){
					newValue.call(this, e);
					throw e;
				}
				return ret;
			} : oldValue;
	});

	ooa.after = md(function(name, newValue, oldValue){
		// summary: creates an "after" advise,
		// it takes no arguments
		return dojo.isFunction(oldValue) ?
			function(){
				var ret;
				try{
					ret = oldValue.apply(this, arguments);
				}finally{
					newValue.call(this);
				}
				return ret;
			} : function(){ newValue.call(this); }
	});
})();
