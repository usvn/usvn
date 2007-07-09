<?php
/**
 * Manipulate subversion of a project
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package svn
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
	private $_project;
	private $_repository;

    /**
    * @param string Project name
    * @throw USVN_Exception if project is invalid
    */
	public function __construct($project)
	{
        $table = new USVN_Db_Table_Projects();
        if (!$table->isAProject($project)) {
            throw new USVN_Exception(T_("Invalid project name %s."), $project);
        }
		$this->_project = $project;
		$this->_repository = Zend_Registry::get('config')->subversion->path . '/svn/' . $this->_project;
	}

	/**
	* @param string Path into subversion directory.
	* @return associative array Array of files or directory included in the path.
	*/
	public function listFile($path)
	{
		return USVN_SVNUtils::listSvn($this->_repository, $path);
	}

	/**
	 * @param string Path to repository
	 * @param int Number of revisiom (0 = no limit)
	 * @return array  Key are revision number example:
	*		array(
	*			1 => array("author" => "duponc_j", "msg" => "Test", date=> 1265413),
	*			2 => array("revision" => "crivis_s", "msg" => "Test2", date=>4565654)
	*		)
	*
	* Date are unix timestamp
	*/
	public function log($limit = 0)
	{
		return USVN_SVNLog::log($this->_repository, $limit);
	}
}
