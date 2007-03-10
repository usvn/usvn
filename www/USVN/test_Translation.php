<?php
/**
* Tests for USVN_Translation
*
* @author Team USVN <contact@usvn.info>
* @link http://www.usvn.info
* @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
* @copyright Copyright 2007, Team USVN
* @since 0.5
* @package usvn
*
** This software has been written in EPITECH <http://www.epitech.net>
** EPITECH is computer science school in Paris - FRANCE -
** under the direction of Flavien Astraud <http://www.epita.fr/~flav>.
** and Jerome Landrieu.
**
** Project : USVN
** Date    : $Date$
*/

set_include_path(get_include_path() . PATH_SEPARATOR . "library");
require_once 'USVN/Translation.php';

class TestTranslation extends PHPUnit_Framework_TestCase
{
    public function test_getLanguage()
    {
		USVN_Translation::initTranslation('fr_FR');
		$this->assertEquals('fr_FR', USVN_Translation::getLanguage());
	}

    public function test_getLocaleDirectory()
    {
		USVN_Translation::initTranslation('fr_FR', 'tests');
		$this->assertEquals('tests', USVN_Translation::getLocaleDirectory());
	}

	public function test_translation()
    {
		USVN_Translation::initTranslation('fr_FR');
		$this->assertEquals("Bienvenue dans USVN", T_("Welcome to USVN"), "Translation error.");
	}
}