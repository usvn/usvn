<?php
/**
 * Generate image tag with correct path
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
    public function Completion($idInput)
    {
	    $front = Zend_Controller_Front::getInstance();
        $view = $front->getParam('view');
		return <<<EOF
		<script>
function ajax_completion()
{
	document.getElementById('completion').style.visibility = 'visible';
	var login = document.getElementById('{$idInput}').value;
	var xhr=null;
    if (window.XMLHttpRequest)
        xhr = new XMLHttpRequest();
    else if (window.ActiveXObject)
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    xhr.onreadystatechange = function() { alert_ajax_completion(xhr); };
	xhr.open("GET", "{$view->url(array('action' => 'completion?txt="+ login + "', 'name' => null))}", true);
	xhr.send(null);
}

function alert_ajax_completion(xhr)
{
	var docXML = xhr.responseXML;
	if (docXML)
	{
		var tabUsers = docXML.getElementsByTagName("table").item(0).firstChild.data;
		document.getElementById('completion').innerHTML = tabUsers;
	}
}

function dumpUser(name)
{
	document.getElementById('{$idInput}').value = name;
	document.getElementById('completion').style.visibility = 'hidden';
}
	</script>
EOF;
    }
}
