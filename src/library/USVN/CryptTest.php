<?php
/**
 * Crypt password
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package Un_package_par_exemple_client
 * @subpackage Le_sous_package_par_exemple_hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_CryptTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'USVN_CryptTest::main');
}

require_once 'PHPUnit/Framework.php';

require_once 'library/USVN/autoload.php';
class USVN_CryptTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('USVN_CryptTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCrypt() {
        $crypt = USVN_Crypt::crypt("toto");
        $this->assertTrue( USVN_Crypt::checkPassword("toto", $crypt));
        $this->assertFalse( USVN_Crypt::checkPassword("tutu", $crypt));

        $crypt = '$apr1$A.IgA/..$vcK1pKAvkEGvAT0ob46Bw0';
        $this->assertTrue( USVN_Crypt::checkPassword("toto", $crypt));;
    }

    public function testApr1MD5()
    {
    	$crypt = USVN_Crypt::_cryptApr1MD5("toto", "A.IgA/..");
    	$this->assertEquals($crypt, '$apr1$A.IgA/..$vcK1pKAvkEGvAT0ob46Bw0');
    	$this->assertEquals($crypt, USVN_Crypt::_cryptApr1MD5("toto", $crypt));

    	$crypt = USVN_Crypt::_cryptApr1MD5("toto", "A.IgA/a.");
    	$this->assertNotEquals($crypt, '$apr1$A.IgA/..$vcK1pKAvkEGvAT0ob46Bw0');
    	$this->assertEquals($crypt, USVN_Crypt::_cryptApr1MD5("toto", $crypt));

    	$crypt = USVN_Crypt::_cryptApr1MD5("toto");
    	$this->assertNotEquals($crypt, '$apr1$A.IgA/..$vcK1pKAvkEGvAT0ob46Bw0');
    	$this->assertEquals($crypt, USVN_Crypt::_cryptApr1MD5("toto", $crypt));

       	$crypt = USVN_Crypt::_cryptApr1MD5("bidulmachinsuperlong");
    	$this->assertEquals($crypt, USVN_Crypt::_cryptApr1MD5("bidulmachinsuperlong", $crypt));
	}

	public function test_genMD5Salt()
	{
		$salt = USVN_Crypt::_genMD5Salt();
		$this->assertEquals(12, strlen($salt));
		$this->assertEquals(substr($salt, 0, 3), '$1$');
		$this->assertEquals($salt[11], '$');
	}
}

// Call USVN_CryptTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'USVN_CryptTest::main') {
    USVN_CryptTest::main();
}
?>
