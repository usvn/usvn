<?php
/**
 * Generate completion
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package helper
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_View_Helper_Completion {
    /**
    * @param Id input for dump user
    */
    public function Completion()
    {
	    $front = Zend_Controller_Front::getInstance();
        $view = $front->getParam('view');
		return <<<EOF
		<script>
		login = 0;
		isselect = 0;
		
function ajax_completion(idx, divcompletion, nameInput, evenement)
{   
	var touche = window.event ? evenement.keyCode : evenement.which;
	if (document.getElementById(nameInput).value != login && touche != 13)
	{
		document.getElementById(divcompletion).style.visibility = 'visible';
		nbcomp = 0;
		nbcur = -1;
		login = document.getElementById(nameInput).value;
		var xhr=null;
    	if (window.XMLHttpRequest)
        	xhr = new XMLHttpRequest();
    	else if (window.ActiveXObject)
        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
    	xhr.onreadystatechange = function() { alert_ajax_completion(xhr, divcompletion); };
		xhr.open("GET", "{$view->url(array('action' => 'completion?txt="+ login + "&idx=" + idx + "&name=" + nameInput + "', 'name' => null))}", true);
		xhr.send(null);
	}
}

function alert_ajax_completion(xhr, divcompletion)
{
	var docXML = xhr.responseXML;
	if (docXML)
	{
		var tabUsers = docXML.getElementsByTagName("tableau").item(0).firstChild.data;
		nbcomp = docXML.getElementsByTagName("nbcomp").item(0).firstChild.data;
		document.getElementById(divcompletion).innerHTML = tabUsers;
	}
}

function dumpInput(name, nameinput, divcompletion)
{
	document.getElementById(nameinput).value = name;
	document.getElementById(divcompletion).style.visibility = 'hidden';
	isselect = 0;
}

function geretouche(evenement, divcompletion, nameInput)
{
	var touche = window.event ? evenement.keyCode : evenement.which;
	if (nameInput == "users_login" || nameInput == "addlogin")
		type = "user";
	else
		type = "grp";
	if (touche == 38 && nbcomp != 0) // haut
	{
		for (i = 0; i < nbcomp; i++)
			document.getElementById(type + i).style.backgroundColor = "white";
				nbcur--;
		if (nbcur < 0)
			nbcur = nbcomp - 1;
		document.getElementById(type + nbcur).style.backgroundColor = "#CCFFFF";
		isselect = 1;
	}
	if (touche == 40 && nbcomp != 0) // bas
	{
		for (i = 0; i < nbcomp; i++)
			document.getElementById(type + i).style.backgroundColor = "white";
		nbcur++;
		if (nbcur > (nbcomp - 1))
			nbcur = 0;
		document.getElementById(type + nbcur).style.backgroundColor = "#CCFFFF";
		isselect = 1;
	}
	if (touche == 13 && nbcomp != 0 && isselect == 1) // entree
			dumpInput(document.getElementById("l" + type + nbcur).innerHTML, nameInput, divcompletion);
}
	</script>
EOF;
    }
}