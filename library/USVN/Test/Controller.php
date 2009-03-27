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

require_once 'library/USVN/autoload.php';

define('USVN_VIEWS_DIR', 'www/views/');
define('USVN_HELPERS_DIR', 'www/helpers/');
define('USVN_CONTROLLERS_DIR', 'www/controllers/');
define('USVN_CONFIG_SECTION', 'general');
define('USVN_ROUTES_CONFIG_FILE', 'www/USVN/routes.ini');


class USVN_Test_Controller extends USVN_Test_DB {
	/* To be overload */
	protected $controller_name;
	protected $controller_class;

	/* ****** */
	protected $request;
	protected $response;
	protected $controller;
	protected $user;
	protected $admin_user;

    protected function setUp() {
		parent::setUp();


		foreach (array_keys($_POST) as $key) {
			unset($_POST[$key]);
		}
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_NonPersistent());

		$this->clean();

		$front = Zend_Controller_Front::getInstance();
		$router = new Zend_Controller_Router_Rewrite();

		$routes_config = new USVN_Config_Ini(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);
		$router->addConfig($routes_config, 'routes');
		$front->setRouter($router);

		$table = new USVN_Db_Table_Users();
		$this->user = $table->fetchNew();
		$this->user->setFromArray(array(
													'users_login' => 'john',
													'users_password' => 'pinocchio'));
		$this->user->save();
		$this->admin_user = $table->fetchNew();
		$this->admin_user->setFromArray(array(
													'users_login' => 'god',
													'users_password' => 'ingodwetrust',
													'users_is_admin' => true));
		$this->admin_user->save();

		$authAdapter = new USVN_Auth_Adapter_Db('john', 'pinocchio');
		Zend_Auth::getInstance()->authenticate($authAdapter);

		$front->setControllerDirectory(USVN_CONTROLLERS_DIR);
		$this->request = new USVN_Controller_Request_Http();
		$front->setRequest($this->request);
		$this->response = new Zend_Controller_Response_Cli();
		$front->setResponse($this->response);
		$router->addRoute('default', new Zend_Controller_Router_Route_Module(array(), $front->getDispatcher(), $front->getRequest()));
	}

	protected function tearDown()
	{
		parent::tearDown();
		$this->clean();
	}

	private function clean()
	{
		Zend_Controller_Front::getInstance()->resetInstance();
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Controller_Action_HelperBroker::resetHelpers();
		$this->request = null;
		$this->response = null;
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
		$this->request->setParam('action', $action);
		$this->request->setDispatched(true);
		require_once 'www/controllers/' . $this->controller_class . '.php';
		$this->controller = new $this->controller_class($this->request, $this->response);
		$this->controller->dispatch($action. "Action");
		$content = ob_get_flush();
	}

	protected function getBody()
	{
		return implode("", $this->response->getBody(true));
	}
}

?>
