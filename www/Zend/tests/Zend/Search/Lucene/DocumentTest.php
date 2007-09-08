<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Document
 */
require_once 'Zend/Search/Lucene/Document.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $document =  new Zend_Search_Lucene_Document();

        $this->assertEquals($document->boost, 1);
    }

    public function testFields()
    {
        $document =  new Zend_Search_Lucene_Document();

        $document->addField(Zend_Search_Lucene_Field::Text('title',      'Title'));
        $document->addField(Zend_Search_Lucene_Field::Text('annotation', 'Annotation'));
        $document->addField(Zend_Search_Lucene_Field::Text('body',       'Document body, document body, document body...'));

        $fieldnamesDiffArray = array_diff($document->getFieldNames(), array('title', 'annotation', 'body'));
        $this->assertTrue(is_array($fieldnamesDiffArray));
        $this->assertEquals(count($fieldnamesDiffArray), 0);

        $this->assertEquals($document->title,      'Title');
        $this->assertEquals($document->annotation, 'Annotation');
        $this->assertEquals($document->body,       'Document body, document body, document body...');

        $this->assertEquals($document->getField('title')->value,      'Title');
        $this->assertEquals($document->getField('annotation')->value, 'Annotation');
        $this->assertEquals($document->getField('body')->value,       'Document body, document body, document body...');

        $this->assertEquals($document->getFieldValue('title'),      'Title');
        $this->assertEquals($document->getFieldValue('annotation'), 'Annotation');
        $this->assertEquals($document->getFieldValue('body'),       'Document body, document body, document body...');


        $document->addField(Zend_Search_Lucene_Field::Text('description', 'Words with umlauts: εγό...', 'ISO-8859-1'));
        $this->assertEquals($document->description, 'Words with umlauts: εγό...');
        $this->assertEquals($document->getFieldUtf8Value('description'), 'Words with umlauts: Γ₯Γ£ΓΌ...');
    }

    public function testHtml()
    {
        $doc =  Zend_Search_Lucene_Document_Html::loadHTML('<HTML><HEAD><TITLE>Page title</TITLE></HEAD><BODY>Document body.</BODY></HTML>');
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $doc->highlight('document', '#66ffff');
        $this->assertEquals($doc->getHTML(), "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\">
<html>
<head><title>Page title</title></head>
<body><p><b style=\"color:black;background-color:#66ffff\">Document</b> body.</p></body>
</html>\n");

        $doc =  Zend_Search_Lucene_Document_Html::loadHTMLFile(dirname(__FILE__) . '/_files/_indexSource/contributing.documentation.html', true);
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $this->assertTrue(array_values($doc->getHeaderLinks()) == 
                          array('index.html', 'contributing.html', 'contributing.bugs.html', 'contributing.wishlist.html'));
        $this->assertTrue(array_values($doc->getLinks()) == 
                          array('contributing.bugs.html',
                                'contributing.wishlist.html',
                                'developers.documentation.html',
                                'faq.translators-revision-tracking.html',
                                'index.html',
                                'contributing.html'));
    }
}

