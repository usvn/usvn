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

    public function test_italic()
    {
        $this->assertEquals('This <i>is a</i> test', Parser::parse("This ''is a'' test"));
    }

    public function test_bold()
    {
        $this->assertEquals('This <b>is a</b> test', Parser::parse("This '''is a''' test"));
    }

    public function test_bolditalic()
    {
        $this->assertEquals('This <b><i>is a</i></b> test', Parser::parse("This '''''is a''''' test"));
    }

    public function test_hr()
    {
        $this->assertEquals('This is a test<hr />Youpi', Parser::parse('This is a test----Youpi'), "Result: #".Parser::parse('This is a test----Youpi')."#\n");
    }

    public function test_head1()
    {
        $test = Parser::parse("= This is a test =
");
        $this->assertEquals('<h1>This is a test</h1>', $test, "Result: #".$test."#\n");
    }

    public function test_head2()
    {
        $test = Parser::parse("== This is a test ==
");
        $this->assertEquals('<h2>This is a test</h2>', $test, "Result: #".$test."#\n");
    }

    public function test_head3()
    {
        $test = Parser::parse("=== This is = a test ===
");
        $this->assertEquals('<h3>This is = a test</h3>', $test, "Result: #".$test."#\n");
    }

    public function test_link()
    {
        $test = Parser::parse('I love http://www.noplay.net YOUPI');
        $this->assertEquals('I love <a href="http://www.noplay.net">http://www.noplay.net</a> YOUPI', $test, "Result: #".$test."#\n");
    }

    public function test_httpsLink()
    {
        $test = Parser::parse('I love https://www.noplay.net YOUPI');
        $this->assertEquals('I love <a href="https://www.noplay.net">https://www.noplay.net</a> YOUPI', $test, "Result: #".$test."#\n");
    }
}
?>