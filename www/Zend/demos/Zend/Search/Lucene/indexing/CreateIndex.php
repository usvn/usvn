<?php
/**
 * @package ZSearch
 * @subpackage demo
 */

/** Zend_Search_Lucene */
require_once 'Zend/Search/Lucene.php';



class FileDocument extends Zend_Search_Lucene_Document
{
    /**
     * Object constructor
     *
     * @param string $fileName
     * @param boolean $storeContent
     * @throws Zend_Search_Lucene_Exception
     */
    public function __construct($fileName, $storeContent = false)
    {
        if (!file_exists($fileName)) {
            throw new Zend_Search_Lucene_Exception("File doesn't exists. Filename: '$fileName'");
        }
        $this->addField(Zend_Search_Lucene_Field::Text('path', $fileName));
        $this->addField(Zend_Search_Lucene_Field::Keyword( 'modified', filemtime($fileName) ));

        $f = fopen($fileName,'rb');
        $byteCount = filesize($fileName);

        $data = '';
        while ( $byteCount > 0 && ($nextBlock = fread($f, $byteCount)) != false ) {
            $data .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($f);

        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('contents', $data));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $data));
        }
    }
}


// Create index
$index = new Zend_Search_Lucene('index', true);
// Uncomment next line if you want to have case sensitive index
// ZSearchAnalyzer::setDefault(new ZSearchTextAnalyzer());

setlocale(LC_CTYPE, 'en_US.ISO8859-1');

$indexSourceDir = 'IndexSource';
$dir = opendir($indexSourceDir);
while (($file = readdir($dir)) !== false) {
    if (is_dir($indexSourceDir . '/' . $file)) {
        continue;
    }
    if (strcasecmp(substr($file, strlen($file)-5), '.html') != 0) {
        continue;
    }

    // Create new Document from a file
    $doc = new FileDocument($indexSourceDir . '/' . $file, true);
    // Add document to the index
    $index->addDocument($doc);

    echo $file . "...\n";
    flush();
}
closedir($dir);


