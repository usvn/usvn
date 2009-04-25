<?php
/**
 * Controller of browser module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package browser
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
include_once('geshi/geshi.php');

class ShowFileController extends USVN_Controller
{
	public function indexAction()
	{
	  $config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
	  $table = new USVN_Db_Table_Projects();
		$project = $table->findByName($this->getRequest()->getParam('project'));
		$project_name = str_replace(USVN_URL_SEP, '/',$this->_request->getParam('project'));
    $relative_file_path = $this->getRequest()->getParam('file');
    $file_path = USVN_SVNUtils::getRepositoryPath($config->subversion->path."/svn/".$project_name."/".$relative_file_path);
    $file_ext = pathinfo($relative_file_path, PATHINFO_EXTENSION);
    $cmd = USVN_SVNUtils::svnCommand("cat --non-interactive $file_path");
    $source = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
    if ($return) {
      throw new USVN_Exception(T_("Can't import into subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
    } else {
      $geshi = new Geshi();
      $geshi->set_language($geshi->get_language_name_from_extension($file_ext), true);
      $geshi->set_source($source);
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
      // style="white-spacere-wrap"
      echo $geshi->parse_code();
    }
	}
}