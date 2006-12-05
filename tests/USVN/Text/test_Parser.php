<?php
/**
* @package text
* @subpackage syntax
*/

require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'USVN/Text/Parser.php';

class TestParser extends PHPUnit2_Framework_TestCase
{
    public function test_stripTags()
    {
        $this->assertEquals( '&lt;b&gt;&lt;a href="link.php"&gt;Un lien&lt;/a&gt;Ceci  est un test&lt;/b&gt;',
                                        Parser::parse('<b><a href="link.php">Un lien</a>Ceci  est un test</b>'));
    }

    public function test_Paragraph()
    {
        $this->assertEquals('First line. Second line.', Parser::parse('First line.
Second line.'));
        $this->assertEquals('First line.<br /><br />Second paragraph.', Parser::parse('First line.

Second paragraph.'));
        $this->assertEquals('First line. Second line.<br /><br />Second paragraph.', Parser::parse('First line.
Second line.

Second paragraph.'));
    }

    public function test_Underline()
    {
        $this->assertEquals('This <u>is a</u> test', Parser::parse('This __is a__ test'));
    }

    public function test_Underline2()
    {
        $this->assertEquals('This <u>is a</u> another <u>test</u> youpi!', Parser::parse('This __is a__ another __test__ youpi!'));
    }

    public function test_Underline3()
    {
        $this->assertEquals('This <u>is a</u> another <u>test  with an _ at the middle</u>', Parser::parse('This __is a__ another __test  with an _ at the middle__'));
    }
}
?>