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
                                        USVN_Text_Parser::parse('<b><a href="link.php">Un lien</a>Ceci  est un test</b>'));
    }

    public function test_Paragraph()
    {
        $this->assertEquals('First line. Second line.', USVN_Text_Parser::parse('First line.
Second line.'));
        $this->assertEquals('First line.<br /><br />Second paragraph.', USVN_Text_Parser::parse('First line.

Second paragraph.'));
        $this->assertEquals('First line. Second line.<br /><br />Second paragraph.', USVN_Text_Parser::parse('First line.
Second line.

Second paragraph.'));
    }

    public function test_Underline()
    {
        $this->assertEquals('This <u>is a</u> test', USVN_Text_Parser::parse('This __is a__ test'));
    }

    public function test_Underline2()
    {
        $this->assertEquals('This <u>is a</u> another <u>test</u> youpi!', USVN_Text_Parser::parse('This __is a__ another __test__ youpi!'));
    }

    public function test_Underline3()
    {
        $this->assertEquals('This <u>is a</u> another <u>test  with an _ at the middle</u>', USVN_Text_Parser::parse('This __is a__ another __test  with an _ at the middle__'));
    }

    public function test_italic()
    {
        $this->assertEquals('This <i>is a</i> test', USVN_Text_Parser::parse("This ''is a'' test"));
    }

    public function test_bold()
    {
        $this->assertEquals('This <b>is a</b> test', USVN_Text_Parser::parse("This '''is a''' test"));
    }

    public function test_strike()
    {
        $this->assertEquals('This <del>is a</del> test', USVN_Text_Parser::parse("This --is a-- test"));
    }

    public function test_bolditalic()
    {
        $this->assertEquals('This <b><i>is a</i></b> test', USVN_Text_Parser::parse("This '''''is a''''' test"));
    }

    public function test_hr()
    {
        $this->assertEquals('This is a test<hr />Youpi', USVN_Text_Parser::parse('This is a test----Youpi'), "Result: #".USVN_Text_Parser::parse('This is a test----Youpi')."#\n");
    }

    public function test_head1()
    {
        $test = USVN_Text_Parser::parse("= This is a test =
");
        $this->assertEquals('<h1>This is a test</h1>', $test, "Result: #".$test."#\n");
    }

    public function test_head2()
    {
        $test = USVN_Text_Parser::parse("== This is a test ==
");
        $this->assertEquals('<h2>This is a test</h2>', $test, "Result: #".$test."#\n");
    }

    public function test_head3()
    {
        $test = USVN_Text_Parser::parse("=== This is = a test ===
");
        $this->assertEquals('<h3>This is = a test</h3>', $test, "Result: #".$test."#\n");
    }

    public function test_link()
    {
        $test = USVN_Text_Parser::parse('I love http://www.noplay.net YOUPI');
        $this->assertEquals('I love <a href="http://www.noplay.net">http://www.noplay.net</a> YOUPI', $test, "Result: #".$test."#\n");
    }

    public function test_link2()
    {
        $test = USVN_Text_Parser::parse('http://www.noplay.net');
        $this->assertEquals('<a href="http://www.noplay.net">http://www.noplay.net</a>', $test, "Result: #".$test."#\n");
    }

    public function test_httpsLink()
    {
        $test = USVN_Text_Parser::parse('I love https://www.noplay.net YOUPI');
        $this->assertEquals('I love <a href="https://www.noplay.net">https://www.noplay.net</a> YOUPI', $test, "Result: #".$test."#\n");
    }


    public  function test_externalLinkWithText()
    {
        $test = USVN_Text_Parser::parse('I love [[http://www.noplay.net|Noplay Network]] YOUPI');
        $this->assertEquals('I love <a href="http://www.noplay.net">Noplay Network</a> YOUPI', $test, "Result: #".$test."#\n");
    }

    public function test_list()
    {
        // For list test espace are useless
        $test = str_replace(' ', '',USVN_Text_Parser::parse('Liste de course:
* un Canon EOS 400D
* Une wii'));
        $search = str_replace(' ', '','Liste de course: <li><ul>un Canon EOS 400D</ul> <ul>Une wii</ul> </li>');
        $this->assertEquals($search, $test, "Result: #".$test."#\n");
    }

    public function test_listTwoLevel()
    {
        // For list test espace are useless
        $test = str_replace(' ', '',USVN_Text_Parser::parse('Liste de course:
* un Canon EOS 400D
** Carte memoire 4 GO
** Grip
* Une wii'));
        $search = str_replace(' ', '','Liste de course: <li><ul>un Canon EOS 400D</ul> <li><ul>Carte memoire 4 GO</ul> <ul>Grip</ul></li> <ul>Une wii</ul> </li>');
        $this->assertEquals($search, $test, "Result: #".$test."#\n");
    }

    public function test_listThreeLevel()
    {
        // For list test espace are useless
        $test = str_replace(' ', '',USVN_Text_Parser::parse('Liste de course:
* un Canon EOS 400D
** Carte memoire 4 GO
** Grip
*** Batterie
*** Couroie
** Objectif
* Une wii'));
        $search = str_replace(' ', '','Liste de course: <li><ul>un Canon EOS 400D</ul><li><ul>Carte memoire 4 GO</ul><ul>Grip</ul><li><ul>Batterie</ul><ul>Couroie</ul></li><ul>Objectif</ul></li><ul>Une wii</ul></li>');
        $this->assertEquals($search, $test, "Result: #".$test."#\n");
    }

    public function test_table()
    {
        $test =  USVN_Text_Parser::parse('||P||H||P||
||My||SQL||Light Database||');
        $search = '<table><tr><td>P</td><td>H</td><td>P</td></tr> <tr><td>My</td><td>SQL</td><td>Light Database</td></tr> </table>';
        $this->assertEquals($search, $test, "Result: #".$test."#\n");
    }

    public function test_table2()
    {
        $test = str_replace(' ', '', USVN_Text_Parser::parse('||M||y||S||Q||L||'));
        $search = '<table><tr><td>M</td><td>y</td><td>S</td><td>Q</td><td>L</td></tr></table>';
        $this->assertEquals($search, $test, "Result: #".$test."#\n");
    }
}
?>