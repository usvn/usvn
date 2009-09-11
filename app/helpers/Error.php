<?php

class USVN_View_Helper_Error {
	public function error($view)
	{
		if (isset($view->message)) {
			return '<div id="error" class="usvn_error">' . $view->message . '<br /><input type="button" value="'.T_('Hide').'" onClick="document.getElementById(\'error\').style.display=\'none\';"></div>';
		} else {
			return '<div id="error"></div>';
		}
	}
}