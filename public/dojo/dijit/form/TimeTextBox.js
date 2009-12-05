dojo.provide("dijit.form.TimeTextBox");

dojo.require("dijit._TimePicker");
dojo.require("dijit.form._DateTimeTextBox");

/*=====
dojo.declare(
	"dijit.form.TimeTextBox.__Constraints",
	[dijit.form._DateTimeTextBox.__Constraints, dijit._TimePicker.__Constraints]
);
=====*/

dojo.declare(
	"dijit.form.TimeTextBox",
	dijit.form._DateTimeTextBox,
	{
		// summary:
		//		A validating, serializable, range-bound time text box with a drop down time picker

		baseClass: "dijitTextBox dijitTimeTextBox",
		popupClass: "dijit._TimePicker",
		_selector: "time",

/*=====
		// constraints: dijit.form.TimeTextBox.__Constraints
		constraints:{},
=====*/

		//	value: Date
		//		The value of this widget as a JavaScript Date object, with only hours/minutes/seconds specified.
		//
		//		Example:
		// |	new dijit.form.TimeTextBox({value: dojo.date.local.fromISOString("T:12:59:59")})
		//
		//		When passed to the parser in markup, must be specified according to locale-independent
		//		`dojo.date.stamp.fromISOString` format.
		//
		//		Example:
		// |	<input dojotype='dijit.form.TimeTextBox' value='T12:34:00'>
		value: new Date("")		// value.toString()="NaN"
	}
);
