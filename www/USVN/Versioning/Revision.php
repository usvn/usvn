<?php
/**
 * Get a precise revision
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package versionning
 * @subpackage revision
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Versioning_Revision
{
	private $_project;
	private $_revision_number;
	private $_revision;

	/**
	 * @param integer Project number
	 * @param integer Revison number
	 */
	public function __construct($project, $revision)
	{
		$this->_project = $project;
		$this->_revision_number = $revision;
		$revisions = new USVN_Db_Table_Revisions();
		$res = $revisions->find($this->_project, $this->_revision_number)->current();
		if ($res === false) {
			throw new USVN_Exception(T_("Invalid revision  project is %s and revision number %d."), $this->_project, $this->_revision_number);
		}
		$this->_revision = $res;
	}

	/**
	 * Get the project id of the revision
	 *
	 * @return integer
	 */
	public function getProject()
	{
		return $this->_project;
	}

	/**
	 * Get the revision number
	 *
	 * @return integer
	 */
	public function getRevisionNumber()
	{
		return $this->_revision_number;
	}

	/**
	 * Get the commit message of a revision
	 *
	 * @return string Commit message
	 */
	public function getMessage()
	{
		return ($this->_revision ->revisions_message);
	}

	/**
	 * Get the author of the revison
	 *
	 * @return Author object (USVN_Db_Table_Row)
	 */
	public function getAuthor()
	{
		$users = new USVN_Db_Table_Users();
		return ($users->find($this->_revision->users_id)->current());
	}

	/**
	 * Get date of the revison
	 *
	 * @return date
	 */
	public function getDate()
	{
		return ($this->_revision->revisions_date);
	}

	/**
	 * Get an iterator on files into this revision
	 *
	 * @return USVN_Versioning_FileVersionSet
	 */
	public function getFiles()
	{
		return new USVN_Versioning_FileVersionSet($this->_project, $this->_revision_number);
	}
}
