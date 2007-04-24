<?php

class USVN_Db_Table_Row_Files extends USVN_Db_Table_Row {
	public function getPath()
	{
		return $this->path;
	}

	public function getType()
	{
		return $this->files_typ_rev;
	}

	/**
	* Get content of the file revision
	*
	* @return string content
	*/
	public function getData()
	{
		//Requete sur la DB ou le filesystem pour r�cuperer le contenu
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
		$properties = new USVN_Db_Table_Files();

		$res = $properties->find(array($this->project, $this->version, $files->files_id, $key));

		return $res->value;
	}
	/**
	* Get version number of the file.
	*
	* @return version number
	*/
	public function getVersion()
	{
		/*
		La vrais version du fichier c'est pas forcement celle pass� au constructeur
		car si je demande la version 42 et que le fichier a pas eu de modif depuis la
		version 10 je doit renvoyer 10.
		*/

		$table = new USVN_Db_Table_Files();

		$result = $table->fetchMaxVersionFiles($this->files_id);

		return $result;
	}
}
