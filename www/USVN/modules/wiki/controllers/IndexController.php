<?php
/**
 * Main controller of the wiki module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package wiki
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
require_once 'USVN/modules/default/controllers/IndexController.php';

class wiki_IndexController extends IndexController
{
	public function preDispatch()
	{
		parent::preDispatch();
		$request = $this->_request;
		$request->setPathInfo(rtrim($request->getPathInfo(), '/'));

		$path = $request->getPathInfo();
		$len = strlen($request->getModuleName());
		$len += strpos($path, $request->getModuleName());
		$path = substr($path, $len);
		if (!$path) {
			$path = '/';
		}
		$path = urldecode($path);

		$this->_view->path = $path;
		$request->setParam('path', $path);

		$actions = array("delete", "edit");
		foreach ($actions as $action) {
			if (isset($_GET[$action])) {
				$request->setActionName($action);
				$request->setParam('action', $action);
				$request->setDispatched(false);
				unset($_GET[$action]);
				break;
			}
		}
	}

	public function viewAction()
	{
		$this->_render('view.html');
	}

	public function editAction()
	{
		$this->viewAction();
	}

	public function deleteAction()
	{
		$this->viewAction();
	}
}
