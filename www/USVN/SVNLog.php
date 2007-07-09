<?php
/**
 * Parse ouput of svn log command
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.4
 * @package client
 * @subpackage utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_SVNLog
{
	/**
	 * @param string Path to repository
	 * @param int Number of revision (0 = no limit)
	 * @return array  Key are revision number example:
	*		array(
	*			1 => array("author" => "duponc_j", "msg" => "Test", date=> 1265413),
	*			2 => array("revision" => "crivis_s", "msg" => "Test2", date=>4565654)
	*		)
	*
	* Date are unix timestamp
	*/
	public static function log($repository, $limit = 0)
	{
        $repository = USVN_SVNUtils::getRepositoryPath($repository);
        if ($limit) {
			$limit = escapeshellarg($limit);
        	$limit = "--limit $limit";
        }
		else {
			$limit = "";
		}
		$message = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe(USVN_SVNUtils::svnCommand("log --xml $limit $repository"), $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't get subversion repository logs: %s"), $message);
		}
		return USVN_SVNLog::parseOutput($message);
	}

	private static function parseOutput($log)
	{
		$res = array();
		$xml = new SimpleXMLElement($log);
		foreach ($xml->logentry as $revision) {
			$res[(int)$revision['revision']] = array(
				"author" => htmlspecialchars((string)$revision->author),
				"msg" => htmlspecialchars((string)$revision->msg),
				"date" =>  strtotime($revision->date)
			);
		}
		return $res;
	}
}
