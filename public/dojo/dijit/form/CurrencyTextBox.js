dojo.provide("dijit.form.CurrencyTextBox");

//FIXME: dojo.experimental throws an unreadable exception?
//dojo.experimental("dijit.form.CurrencyTextBox");

dojo.require("dojo.currency");
dojo.require("dijit.form.NumberTextBox");

/*=====
dojo.declare(
	"dijit.form.CurrencyTextBox.__Constraints",
	[dijit.form.NumberTextBox.__Constraints, dojo.currency.__FormatOptions, dojo.currency.__ParseOptions]
);
=====*/

dojo.declare(
	"dijit.form.CurrencyTextBox",
	dijit.form.NumberTextBox,
	{
		// summary:
		//		A validating currency textbox

		// currency: String
		//		the [ISO4217](http://en.wikipedia.org/wiki/ISO_4217) currency code, a three letter sequence like "USD"
		currency: "",

		/*=====
		// constraints: dijit.form.CurrencyTextBox.__Constraints
		//		Minimum/maximum amount allowed.
		constraints: {},
		======*/

		// Override regExpGen ValidationTextBox.regExpGen().... we use a reg-ex generating function rather
		// than a straight regexp to deal with locale  (plus formatting options too?)
		regExpGen: dojo.currency.regexp,

		// Override NumberTextBox._formatter to deal with currencies, ex: converts "123.45" to "$123.45"
		_formatter: dojo.currency.format,

/*=====
		parse: function(value, constraints){
			// summary:
			//		Parses string as a Currency, according to constraints
			// value: String
			//		The currency represented as a string
			// constraints: dojo.currency.__ParseOptions
			// tags:
			//		protected

			return 123.45;		// Number
		},
=====*/
		parse: dojo.currency.parse,

		postMixInProperties: function(){
			this.constraints.currency = this.currency;
			this.inherited(arguments);
		}
	}
);
