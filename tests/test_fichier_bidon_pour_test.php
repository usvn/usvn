<?php
require_once 'PHPUnit/Framework/TestCase.php';

$path = '/usr/lib/pear';
set_include_path('../' . PATH_SEPARATOR . $path);

require_once 'fichier_bidon_pour_test.php';

class TestBidon extends PHPUnit_Framework_TestCase
{
    public function test_returnVrais()
    {
        $bidon = new Bidon();
        $this->assertEquals($bidon->returnVrais(), true);
    }
}
?>
