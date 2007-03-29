<?php

/**
 * Get a file at a precise version
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package versionning
 * @subpackage file
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_Versioning_FileVersion
{
	private $path;
	private $version;
	private $project;
	private $files_id;

	/**
	* @param int Project id
	* @param string Path of the file into repository
	* @param integer Request version
	*/
	public function __construct($project, $path, $version)
	{
		$this->path = $path;
		$this->project = $project;
		//On recherche le fichier dans la DB et on met $this->version à la version trouvé (Voir getVersion pour plus d'explication)
		//$this->version = getversion();
		$table = new USVN_modules_default_models_Files();

		$result = $table->fetchMaxVersionFiles($this->files_id);
		
		$this->version = $result->revisions_num;

		//on recherche l'id du fichier en private
		$files = new USVN_modules_default_models_Files();
		$files = $table->fetchRow('path =' . $this->path);
		$this->files_id = $files->files_id;
	}

	/**
	* Get path of the file into the repository
	*
	* @return string
	*/
	public function getPath()
	{
		return $this->path;
	}

	/**
	* Get type of operation: M, A or D
	*/
	public function getType()
	{
		$files = new USVN_modules_default_models_Files();
		
		$res = $files->find(array("projects_id"=>$this->project,"revisions_num"=>$this->version,"files_id"=>$this->files_id ));
		
		return ($res->type);
	}

	/**
	* Get content of the file revision
	*
	* @return string content
	*/
	public function getData()
	{
		//Requete sur la DB ou le filesystem pour récuperer le contenu
		return file_get_contents($this->path);
	}

	/**
	* Get meta data (property) of the file revision
	*
	* @param Property name
	* @return string
	*/
	public function getMeta($key)
	{
		//Requete sur la DB pour recuperer la propriete demandé

		$properties = new USVN_modules_default_models_Properties();

		$res = $properties->find(array("projects_id"=>$this->project,"revisions_num"=>$this->version,"files_id"=>$files->files_id,"properties_name"=>$key));

		return($res->value);

	}

	/**
	* Get version number of the file.
	*
	* @return version number
	*/
	public function getVersion()
	{
		/*
		La vrais version du fichier c'est pas forcement celle passé au constructeur
		car si je demande la version 42 et que le fichier a pas eu de modif depuis la
		version 10 je doit renvoyer 10.
		*/

		$table = new USVN_modules_default_models_Files();

		$result = $table->fetchMaxVersionFiles($this->files_id);

		return $result;
	}
}

?>
