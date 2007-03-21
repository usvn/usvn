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
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

 class USVN_Config extends Zend_Config_Ini
 {
	private $_filename;

	public function __construct($filename, $section, $config = true)
	{
		$this->_filename = $filename;
		parent::__construct($filename, $section, $config);
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
	*
	* @todo translate exception
	*/
	public function save()
	{
		$f = fopen($this->_filename, 'w');
		if (!$f) {
			throw new USVN_Exception("Can't open {$this->filename} for write.");
		}
		fwrite($f, "[".$this->getSectionName()."]\n");
		$this->dumpLevel($f, "", $this);
        fclose($f);
	}
 }
