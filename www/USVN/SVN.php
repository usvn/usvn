<?php
/**
 * Class with usefull static method for console.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage console
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: SVN.php 386 2007-05-10 13:33:26Z billar_m $
 */
class USVN_SVN
{
	private $m_project;

	function __construct($project)
	{
		$m_project = $project;
	}

	/**
	* @param string Path of subversion directory.
	* @return associative array Array of files or directory included in the path.
	*/
	static public function listFile($path)
	{
		/* Code fake for now */
		$tab= array(array("name" => 'filetoto.txt', "isDirectory" => false),
					array("name" => 'filetiti.txt', "isDirectory" => false),
					array("name" => 'directorybla', "isDirectory" => true));
		return $tab;
	}
}
