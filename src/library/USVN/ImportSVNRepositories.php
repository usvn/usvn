<?php
/**
 * Import users from an htpasswd file to USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package admin
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: ImportSVNRepositories.php 1188 2007-10-06 12:03:17Z dolean_j $
 */

class USVN_ImportSVNRepositories
{
	private $_repos = array();
	private $_repos_imported = array();

	/**
	 * Check if a SVN repository can be imported
	 *
	 * @param string $path
	 * @throws USVN_Exception
	 * @return bool
	 */
	static public function canBeImported($path, $options = array('verbose' => false))
	{
		if (!is_string($path)) {
			throw new USVN_Exception(T_('%s must be a string: %s'), '$path');
		}
		$config = Zend_Registry::get('config');
		$path = realpath($path);
		if (USVN_DirectoryUtils::firstDirectoryIsInclude($path, $config->subversion->path)) {
			if (is_dir($path) && USVN_SVNUtils::isSVNRepository($path)) {
				return true;
			} elseif (isset($options['verbose']) && $options['verbose'] == true) {
				if (!is_dir($path)) {
					print "'$path' is not a directory.\n";
				} elseif (!USVN_SVNUtils::isSVNRepository($path)) {
					print "'$path' is not a SVN repository\n";
				}
			}
		} elseif (isset($options['verbose']) && $options['verbose'] == true) {
			print "'$path' is not in subversion's path ({$config->subversion->path}).\n";
		}
		return false;
	}

	/**
	 * Look after SVN repositories to import into USVN
	 *
	 * @param string $path
	 * @param array $options array(recursive => true|false)
	 * @todo allow to set n level of recursion in options
	 * @throws USVN_Exception
	 * @return array
	 */
	static public function lookAfterSVNRepositoriesToImport($path, $options = array()) {
		$results = array();
		if (USVN_ImportSVNRepositories::canBeImported($path, $options)) {
			return (array($path));
		}
		$folders = USVN_DirectoryUtils::listDirectory($path);
		foreach ($folders as $folder) {
			$current_path = $path . DIRECTORY_SEPARATOR . $folder;
			if (USVN_ImportSVNRepositories::canBeImported($current_path, $options)) {
				$results[] = $current_path;
			} else {
				if (isset($options['recursive']) && $options['recursive'] == true) {
					$results = array_merge($results, USVN_ImportSVNRepositories::lookAfterSVNRepositoriesToImport($current_path, $options));
				}
			}
		}
		return $results;
	}

	/**
	 * Add SVN Repositor(y|ies) in list in order to import with importSVNRepositories()
	 *
	 * @param mixed $path(s)
	 * @param array $options = array('login' => $name,
	 * 									'name' => '',
	 * 									'description' => '',
	 * 									$options['creategroup'] = true|false,
	 * 									$options['addmetogroup'] = true|false,
	 * 									$options['admin'] = true|false)
	 * @return array
	 */
	public function addSVNRepositoriesToImport($path, $options = array('login' => 0,
																		'name' => '',
																		'description' => '',
																		'creategroup' => false,
																		'addmetogroup' => false,
																		'admin' => false))
	{
		$config = Zend_Registry::get('config');
		$svnPath = str_replace('\\', '/', realpath($config->subversion->path));
		if (is_array($path)) {
			foreach ($path as $p) {
				$p = realpath($p);
				if (is_string($p) && $this->canBeImported($p, $options)) {
					$p = str_replace('//', '/', str_replace('\\', '/', $p));
					$name = isset($options['name']) && !empty($options['name']) ? $options['name'] :
							str_replace($svnPath, '', $p);
					$name = trim($name, '/');
					if (isset($options['verbose']) && $options['verbose'] == true) {
						print "Project's name for '$p' will be '$name'\n";
					}
					$options['description'] = isset($options['description']) ? $options['description'] : '';
					$options['creategroup'] = isset($options['creategroup']) ? $options['creategroup'] : false;
					$options['addmetogroup'] = isset($options['addmetogroup']) ? $options['addmetogroup'] : false;
					$options['admin'] = isset($options['admin']) ? $options['admin'] : false;
					$this->_repos[] = array('name' => $name, 'path' => $p, 'options' => $options);
				}
			}
		} elseif (is_string($path) && $this->canBeImported($path, $options)) {
			$this->addSVNRepositoriesToImport(array($path), $options);
		}
		return $this->_repos;
	}

	/**
	 * Import all SVN Repositories added
	 *
	 * @throws USVN_Exception
	 * @return array USVN_Db_Table_Row_Project
	 */
	public function importSVNRepositories()
	{
		if (count($this->_repos)) {
			foreach ($this->_repos as $k => $repo) {
				$project = USVN_Project::createProject(array('projects_name' => $repo['name'],
																'projects_description' => $repo['options']['description']),
														$repo['options']['login'],
														$repo['options']['creategroup'],
														$repo['options']['addmetogroup'],
														$repo['options']['admin'],
														0);//0, we don't want to create branch's, trunk's and tag's directory
				//no need to check if the project have been created, an exception will be throw in this case
				$this->_repos_imported[] = $project;
				unset($this->_repos[$k]);
			}
		}
		return $this->_repos_imported;
	}

	/**
	 * Get list of imported SVN repositories
	 *
	 * @return array() USVN_Db_Table_Row_Project
	 */
	public function getSVNRepositoriesList()
	{
		return $this->_repos_imported;
	}
}
