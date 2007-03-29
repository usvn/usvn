<?php
/**
 * A set of USVN_Versioning_FileVersion
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
class USVN_Versioning_FileVersionSet implements Iterator, countable
{
	private $project;
	private $revision;
	/**
	 * Enter description here...
	 *
	 * @var Zend_Db_Table_Rowset
	 */
	private $list_files;

	/**
	* @param integer Project number
	* 
	* @param integer Revison number
	*/
	public function __construct($project, $revision)
	{
		$this->project = $project;
		$this->revision = $revision;
				
		$table = new USVN_modules_default_models_Files();

		$this->list_files = $table->fetchGetFiles($this->project, $this->revision);
	}

	/**
	* Get the project id of the set of files
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
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function rewind()
    {

		return $this->list_files->rewind();
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return USVN_Versioning_FileVersion
     */
    public function current()
    {
    	return $this->list_files->current();
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {
    	return $this->list_files->key();
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int The next pointer value.
     */
    public function next()
    {
    	return $this->list_files->next();
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
    	return $this->list_files->valid();
    }
    
        /**
     * Returns the number of elements in the collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->list_files->count();
    }
    
     /**
     * Returns true if $this->count > 0, false otherwise.
     * Required by interface Countable.
     *
     * @return bool
     */
    public function exists()
    {
        return $list_files->count() > 0;
    }
}
