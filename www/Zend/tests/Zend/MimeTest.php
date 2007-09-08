<?php
/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */


/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */
class Zend_MimeTest extends PHPUnit_Framework_TestCase
{
    public function testBoundary()
    {
        // check boundary for uniqueness
        $m1 = new Zend_Mime();
        $m2 = new Zend_Mime();
        $this->assertNotEquals($m1->boundary(), $m2->boundary());

        // check instantiating with arbitrary boundary string
        $myBoundary = 'mySpecificBoundary';
        $m3 = new Zend_Mime($myBoundary);
        $this->assertEquals($m3->boundary(), $myBoundary);

    }

    public function testIsPrintable_notPrintable()
    {
        $this->assertFalse(Zend_Mime::isPrintable('Test with special chars: äüßöÖ'));
    }

	public function testIsPrintable_isPrintable()
	{
    	$this->assertTrue(Zend_Mime::isPrintable('Test without special chars'));
	}

    public function testQP()
    {
        $text = "This is a cool Test Text with special chars: äöüß\n"
              . "and with multiple linesäöüß some of the Lines are long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long and with äöüß";

        $qp = Zend_Mime::encodeQuotedPrintable($text);
        $this->assertEquals(quoted_printable_decode($qp), $text);
    }

    public function testBase64()
    {
        $content = str_repeat("\x88\xAA\xAF\xBF\x29\x88\xAA\xAF\xBF\x29\x88\xAA\xAF", 4);
        $encoded = Zend_Mime::encodeBase64($content);
        $this->assertEquals($content, base64_decode($encoded));
    }

    public function testZf1058WhitespaceAtEndOfBodyCausesInfiniteLoop()
    {
        $mail = new Zend_Mail();
        $mail->setSubject('my subject');
        $mail->setBodyText("my body\r\n\r\n...after two newlines\r\n ");
        $mail->setFrom('test@email.com');
        $mail->addTo('test@email.com');

        // test with generic transport
        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);
        $body = quoted_printable_decode($mock->body);
        $this->assertContains("my body\r\n\r\n...after two newlines", $body, $body);
    }
}
