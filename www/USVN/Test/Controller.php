<?php
/**
 * Base class for test controllers
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package test
 * @subpackage db
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: DB.php 1014 2007-07-11 13:26:14Z duponc_j $
 */
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

define('USVN_VIEWS_DIR', 'www/views/');
define('USVN_HELPERS_DIR', 'www/helpers/');
define('USVN_CONTROLLERS_DIR', 'www/controllers/');


class USVN_Test_Controller extends USVN_Test_DB {
	protected $controller_name;
	protected $controller_class;
	protected $request;
	protected $response;

    protected function setUp() {
		parent::setUp();
		$table = new USVN_Db_Table_Users();
		$obj = $table->fetchNew();
		$obj->setFromArray(array(
													'users_login' => 'user',
													'users_password' => 'password'));
		$obj->save();

		$front = Zend_Controller_Front::getInstance();
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_NonPersistent());
		$authAdapter = new USVN_Auth_Adapter_Db('user', 'password');
		Zend_Auth::getInstance()->authenticate($authAdapter);
		$front->setControllerDirectory(USVN_CONTROLLERS_DIR);
		$this->request = new USVN_Controller_Request_Http();
		$this->response = new Zend_Controller_Response_Cli();
	}

	/**
	* Run action
	*
	* @param string Controller name
	* @param string Action name
	*/
	protected function runAction($action)
	{
		$this->request->setControllerName($this->controller_name);
		$this->request->setActionName($action);
		$this->request->setDispatched(true);;
		require_once 'www/controllers/' . $this->controller_class . '.php';
		$controller = new $this->controller_class($this->request, $this->response);
		$controller->dispatch($action. "Action");
		$content = ob_get_flush();
	}

	protected function getBody()
	{
		return implode("", $this->response->getBody(true));
	}
}

?>
