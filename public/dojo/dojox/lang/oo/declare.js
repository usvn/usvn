dojo.provide("dojox.lang.oo.declare");

dojo.experimental("dojox.lang.oo.mixin");

// a copy of the version #7 (with makeDeclare() yet a drop-in replacement for dojo.declare())

(function(){
	var d = dojo, oo = dojox.lang.oo, op = Object.prototype,
		isF = d.isFunction, xtor = function(){}, extraNames, i,

		each = function(a, f){ for(var i = 0, l = a.length; i < l; ++i){ f(a[i]); } },

		error = function(cond, msg){ if(cond){ throw new Error("declare: " + msg); } },

		mix = function(target, source, name){
			var t = target[name], s = source[name];
			return t !== s && s !== op[name] ? target[name] = s : 0;
		},

		mixName = function(target, source, name){
			var t = mix(target, source, name);
			if(isF(t)){ t.nom = name; }
		},

		mixer = function(target, source, mix){
			for(var name in source){ mix(target, source, name); }
			each(extraNames, function(name){ if(name in source) mix(target, source, name); });
		},

		collect = function(meta, base){
			var m = base._meta, mb = meta.bases;
			m && mb.push(m.bases);
			mb.push(base);
		};

	for(i in {toString: 1}){ extraNames = []; break; }
	extraNames = extraNames || ["hasOwnProperty", "valueOf", "isPrototypeOf",
			"propertyIsEnumerable", "toLocaleString", "toString"];

	oo.makeDeclare = function(before, after){

		var chains = {constructor: "after"},

			buildMethodList = function(meta, name){
				var fs = [], mb = meta.bases, i, l, t, c, m, h;
				for(i = 0, l = mb.length; i < l; ++i){
					// the assignment on the next line is intentional
					(t = (c = mb[i]) && (m = c._meta) && (h = m.hidden) ?
						(name in h) && h[name] : c.prototype[name]) && fs.push(t);
				}
				// the assignment on the next line is intentional
				(t = meta.hidden[name]) && fs.push(t);
				// the next line works for inherited methods too
				return chains[name] === "after" ? fs : fs.reverse();
			},

			inherited = function(name, args, a){
				var c = this.constructor, m = c._meta, cache = c._cache, caller,
					i, l, f, n, ch, s, x;
				// crack arguments
				if(typeof name != "string"){
					a = args;
					args = name;
					name = "";
				}
				caller = inherited.caller;
				n = caller.nom;
				error(n && name && n !== name, "calling inherited() with a different name: " + name);
				name = name || n;
				ch = cache[name];
				// get the cached method list
				if(!ch){
					error(!name, "can't deduce a name to call inherited()");
					error(typeof chains[name] == "string", "chained method: " + name);
					ch = cache[name] = buildMethodList(m, name);
				}
				// check the stack
				do{
					s = this._inherited, n = s.length - 1;
					if(n >= 0){
						x = s[n];
						if(x.name === name && ch[x.pos] === caller && caller.caller === inherited){
							break;
						}
					}
					// find the caller
					for(i = 0, l = ch.length; i < l && ch[i] !== caller; ++i);
					if(i == l){
						// the assignment on the next line is intentional
						this[name] === caller && (i = -1) || error(1, "can't find the caller for inherited()");
					}
					// the assignment on the next line is intentional
					s.push(x = {name: name, start: i, pos: i});
				}while(false);
				f = ch[++x.pos];
				try{
					return f ? f.apply(this, a || args) : undefined;
				}finally{
					x.start == --x.pos && s.pop();
				}
			};

		before = before || [];
		each(before, function(name){ chains[name] = "before"; });
		after  = after  || [];
		each(after,  function(name){ chains[name] = "after";  });

		return function(className, superclass, props){
			var mixins, proto, i, l, t, f, ctor, hidden = {}, meta = {bases: []};

			// crack parameters
			if(typeof className != "string"){
				props = superclass;
				superclass = className;
				className = "";
			}
			if(d.isArray(superclass)){
				mixins = superclass;
				superclass = mixins[0];
			}

			// build a prototype
			if(superclass){
				collect(meta, superclass);
				if(mixins){
					for(i = 1, l = mixins.length; i < l; ++i){
						// the assignment on the next line is intentional
						error(!(t = mixins[i]), "mixin #" + i + " is null");
						collect(meta, t);
						// delegation
						xtor.prototype = superclass.prototype;
						proto = new xtor;
						mixer(proto, t.prototype, mix);
						(ctor = function(){}).superclass = superclass;
						ctor.prototype = proto;
						superclass = proto.constructor = ctor;
					}
				}
				// add props
				xtor.prototype = superclass.prototype;
				proto = new xtor;
			}else{
				proto = {};
			}

			// add props
			// the assignment on the next line is intentional
			mixer(proto, (meta.hidden = props || {}), mixName);

			// flatten the base list and collect our chain instructions
			meta.bases = meta.bases.concat.apply([], meta.bases);

			// build chains and add them to the prototype
			each(after.concat(before), function(name){
				// the assignment on the next line is intentional
				(proto[name] = function(){
					var c = this.constructor, t = buildMethodList(c._meta, name), l = t.length,
						f = function(){ for(var i = 0; i < l; ++i){ t[i].apply(this, arguments); } };
					f.nom = name;
					// the assignment on the next line is intentional
					(c.prototype[name] = f).apply(this, arguments);
				}).nom = name;
			});

			// add inherited to the prototype
			proto.inherited = inherited;

			// build ctor
			t = buildMethodList(meta, "constructor");
			ctor = function(){
				this._inherited = [];

				// perform the shaman's rituals of the original dojo.declare()

				// 1) call two types of the preamble
				var a = arguments, args = a, a0 = a[0], f, i, l;
				// the assignment on the next line is intentional
				a = a0 && (f = a0.preamble) && f.apply(this, a) || a;
				// the assignment on the next line is intentional
				a = (f = this.preamble) && f.apply(this, a) || a;

				// 2) call the constructor with different parameters
				for(i = 0, l = t.length - 1; i < l; ++i){
					t[i].apply(this, a);
				}
				l >= 0 && t[i].apply(this, t[i] === ctor._meta.hidden.constructor ? args : a);

				// 3) continue the original ritual: call the postscript
				// the assignment on the next line is intentional
				(f = this.postscript) && f.apply(this, args);
			};

			// build metadata on the constructor
			ctor._meta  = meta;
			ctor._cache = {};
			ctor.superclass = superclass && superclass.prototype;

			proto.constructor = ctor;
			ctor.prototype = proto;

			// the assignment on the next line is intentional
			className && d.setObject(proto.declaredClass = className, ctor);

			return ctor;	// Function
		};
	};

	/*=====
	//	summary:
	//		Create a feature-rich constructor from compact notation
	//	className: String?:
	//		The optional name of the constructor (loosely, a "class")
	//		stored in the "declaredClass" property in the created prototype
	//	superclass: Function|Function[]:
	//		May be null, a Function, or an Array of Functions. If an array,
	//		the first element is used as the prototypical ancestor and
	//		any following Functions become mixin ancestors.
	//	props: Object:
	//		An object whose properties are copied to the created prototype.
	//		Add an instance-initialization function by making it a property
	//		named "constructor".
	//	description:
	//		Create a constructor using a compact notation for inheritance and
	//		prototype extension.
	//
	//		Mixin ancestors provide a type of multiple inheritance. Prototypes of mixin
	//		ancestors are copied to the new class: changes to mixin prototypes will
	//		not affect classes to which they have been mixed in.
	//
	//		"className" is cached in "declaredClass" property of the new class.
	//
	//	example:
	//	|	dojox.lang.oo.declare("my.classes.bar", my.classes.foo, {
	//	|		// properties to be added to the class prototype
	//	|		someValue: 2,
	//	|		// initialization function
	//	|		constructor: function(){
	//	|			this.myComplicatedObject = new ReallyComplicatedObject();
	//	|		},
	//	|		// other functions
	//	|		someMethod: function(){
	//	|			doStuff();
	//	|		}
	//	|	});
	=====*/
	oo.declare = oo.makeDeclare();
})();
