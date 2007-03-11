<?php

/**
* Get a file at a precise version
*/
class USVN_Versioning_FileVersion
{
	private $path;
	private $version;
	private $project;

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
	* Get content of the file revision
	*
	* @return string content
	*/
	public function getData()
	{
		//Requete sur la DB ou le filesystem pour récuperer le contenu
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
		return $this->version;
	}
}

?>