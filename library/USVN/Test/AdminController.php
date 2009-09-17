<?php
/**
 * Base class for test admin controllers
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

class USVN_Test_AdminController extends USVN_Test_Controller
{
	protected function setUp()
	{
		parent::setUp();
		$authAdapter = new USVN_Auth_Adapter_Database('god', 'ingodwetrust');
		Zend_Auth::getInstance()->authenticate($authAdapter);
	}
}
