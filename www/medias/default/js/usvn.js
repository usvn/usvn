
/**
 * USVN's default file
 * Required for all javascript components
 */

//Include function for javascript (in the header)
function includeJsFile(file) {
  var head = document.getElementsByTagName('head')[[0]];
  var headInc = document.createElement('script');
  headInc.setAttribute('type', 'text/javascript');
  headInc.setAttribute('src', file);
  head.appendChild(headInc);
}

function selectObjectForm(obj) {
	chk = document.getElementsByName(obj);
//	chk.selectedIndex = "all";
//	for (var i = 0; i < chk.length; i++) {
//		chk[i].selected = 'selected';
//	}
}

function deSelectObjectForm(obj) {
	chk = document.getElementsByName(obj);
//	chk.selectedIndex = "0";
//	for (var i = 0; i < chk.length; i++) {
//		chk[i].selected = '';
//	}
}

//Call for HTMLTableTools
includeJsFile("<?php echo $this->js_default_directory; ?>/tools/prototype.js");
includeJsFile("<?php echo $this->js_default_directory; ?>/tools/builder.js");
includeJsFile("<?php echo $this->js_default_directory; ?>/tools/HTMLTableTools/HTMLTableTools.js");

//HTMLTableTools default options's definition
var HTMLTableToolsOptions = {
	skipCells: [],
	pathToImgs: "<?php echo $this->js_default_directory; ?>/tools/HTMLTableTools/",
	multiColSort: true,
	reorderCol: false,
	showSortIndex: true,
	highlight: true,
	highlightColumn: false,
	selectRow: true,
	multipleSelect: true
};

// Find all tables with class sortable and make them sortable with HTMLTableTools
function lookForHTMLTableTools(options)
{
	if (!document.getElementsByTagName) {
		return;
	}
	tbls = document.getElementsByTagName("table");
	for (ti = 0; ti < tbls.length; ti++) {
		thisTbl = tbls[ti];
		if (((' ' + thisTbl.className + ' ').indexOf("sortable") != -1) && (thisTbl.id)) {
			var myTable = new HTMLTableTools(thisTbl.id, options);
		}
	}
}

/* End of usvn.js */
