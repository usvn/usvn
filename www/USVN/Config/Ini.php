<?php
/**
 * Class to manipulate config file
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 * @subpackage config
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

 class USVN_Config_Ini extends Zend_Config_Ini
 {
	private $_filename;

	/**
	* If $config['create'] = true the config will be create if he doesn't exist.
	*
	* @param string Path to config file
	* @param string Section of config file
	* @param array Configuration array
	* @throw USVN_Exception
	*/
	public function __construct($filename, $section, $config = array())
	{
		$this->_filename = $filename;
		if (!file_exists($filename)) {
			if (isset($config['create']) && $config['create'] === true) {
				if (@file_put_contents($filename, "[$section]\n") === false) {
					throw new USVN_Exception("Can't write config file %s.", getcwd() . DIRECTORY_SEPARATOR . $filename);
				}
			}
			else {
				throw new USVN_Exception("Can't open config file %s.", getcwd() . DIRECTORY_SEPARATOR . $filename);
			}
		}
		try {
			parent::__construct($filename, $section, true);
		}
		catch (Exception $e) {
			throw new USVN_Exception($e->getMessage());
		}
	}

	private function dumpLevel($handle, $prefix, $data)
	{
        foreach ($data->_data as $key => $value) {
            if (is_object($value)) {
               $this->dumpLevel($handle, "$prefix$key.", $value);
            } else {
				fwrite($handle, "$prefix$key = \"$value\"\n");
            }
        }
	}

	/**
	* Save change on the config file
	*/
	public function save()
	{
		$f = @fopen($this->_filename, 'w');
		if (!$f) {
			throw new USVN_Exception(T_("Can't write config file."));
		}
		fwrite($f, "[".$this->getSectionName()."]\n");
		$this->dumpLevel($f, "", $this);
        fclose($f);
	}
 }
