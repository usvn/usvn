<?php
/**
 * Query Flickr for a tag and display all of the photos for
 * that tag.
 */

error_reporting(E_ALL);

require_once 'Zend/Service/Flickr.php';

$flickr = new Zend_Service_Flickr('your api key here');

$photos = $flickr->tagSearch('php');


foreach ($photos as $photo) {
    echo '<img src="' . $photo->Thumbnail->uri . '" /> <br />';
	echo $photo->title . "<br /> \n";
}
