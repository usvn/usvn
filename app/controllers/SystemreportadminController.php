<?php
/**
 * Controller for system report admin page
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: SystemreportadminController.php 1340 2007-11-15 17:41:30Z duponc_j $
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class SystemreportadminController extends AdminadminController
{
	public function indexAction()
	{
		$this->view->config = Zend_Registry::get('config');
		$this->view->subversionversion = implode('.', USVN_SVNUtils::getSvnVersion());
	}
}
