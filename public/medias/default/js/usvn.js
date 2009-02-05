/**
 * USVN's default file
 * Required for all javascript components
 */

function selectObjectForm(obj) {
	var chk = document.getElementById(obj);
	for (var i = 0; i < chk.length; i++) {
		chk.options[i].selected = true;
	}
}

function deSelectObjectForm(obj) {
	var chk = document.getElementById(obj);
	for (var i = 0; i < chk.length; i++) {
		chk.options[i].selected = false;
	}
}

/* End of usvn.js */
