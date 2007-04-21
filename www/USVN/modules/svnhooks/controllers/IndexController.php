<?php
/**
 * Main controller of the svnhooks modules
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package svnhooks
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

class svnhooks_IndexController extends Zend_Controller_Action
{
	protected $_mimetype = 'text/xml';

	/**
	 * Default action for every controller.
	 *
	 */
	public function indexAction() {
		$server = new Zend_XmlRpc_Server();
		Zend_XmlRpc_Server_Fault::attachFaultException('USVN_Exception');
		$server->setClass('USVN_modules_svnhooks_models_Hooks', 'usvn.client.hooks');
		echo $server->handle();
	}

}
