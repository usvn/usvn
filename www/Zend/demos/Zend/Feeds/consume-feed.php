<?php
/**
 * Consume an RSS feed and display all of the titles and 
 * associated links within.
 */

require_once 'Zend/Feed.php';

$feed = Zend_Feed::import('http://news.google.com/?output=rss');

foreach ($feed->items as $item) {
    
    echo "<p>" . $item->title() . "<br />";
    echo $item->link()  . "</p>";
    
}

