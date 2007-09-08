<?php
require_once 'Zend/Feed.php';
require_once 'Zend/Search/Lucene.php';

//create the index
$index = new Zend_Search_Lucene('/tmp/feeds_index', true);

// index each item
$rss = Zend_Feed::import('http://feeds.feedburner.com/ZendDeveloperZone');

foreach ($rss->items as $item) {
    $doc = new Zend_Search_Lucene_Document();

    if ($item->link && $item->title && $item->description) {

        $link = htmlentities(strip_tags( $item->link() ));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('link', $link));

        $title = htmlentities(strip_tags( $item->title() ));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $title));

        $contents = htmlentities(strip_tags( $item->description() ));
        $doc->addField(Zend_Search_Lucene_Field::Text('contents', $contents));

        echo "Adding {$item->title()}...\n";
        $index->addDocument($doc);
    }
}

$index->commit();
