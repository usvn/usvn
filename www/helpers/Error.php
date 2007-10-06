<?php

class USVN_View_Helper_Error {
	public function error($view)
	{
		if (isset($view->message)) {
			return '<div id="error" class="usvn_error">' . $view->message . '</div>';
		} else {
			return '<div id="error"></div>';
		}
	}
}