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
	private $project;
	private $revision;

	/**
	 * @param integer Project number
	 * @param integer Revison number
	 */
	public function __construct($project, $revision)
	{
		$this->project = $project;
		$this->revision = $revision;
	}

	/**
	 * Get the project id of the revision
	 *
	 * @return integer
	 */
	public function getProject()
	{
		return $this->project;
	}

	/**
	 * Get the revision number
	 *
	 * @return integer
	 */
	public function getRevisionNumber()
	{
		return $this->revision;
	}

	/**
	 * Get the commit message of a revision
	 *
	 * @return string Commit message
	 */
	public function getMessage()
	{
		$revision = new USVN_modules_default_models_Revisions();

		//		$res = $revision->find(array("projects_id" => $this->project, "revisions_num" => $this->revision));
		$res = $revision->find($this->project, $this->revision);

		return ($res->current()->revisions_message);
	}

	/**
	 * Get the author of the revison
	 *
	 * @return integer Author id
	 */
	public function getAuthor()
	{
		$revision = new USVN_modules_default_models_Revisions();
		$res = $revision->find($this->project, $this->revision);

		return ($res->current()->users_id);
		
	}

	/**
	 * Get an iterator on files into this revision
	 *
	 * @return USVN_Versioning_FileVersionSet
	 */
	public function getFiles()
	{
		return new USVN_Versioning_FileVersionSet($this->project, $this->revision);
	}
}
