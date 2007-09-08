<?php
require_once 'Zend/Search/Lucene.php';

$index = new Zend_Search_Lucene('/tmp/feeds_index');
echo "Index contains {$index->count()} documents.\n";

$search = 'php';
$hits   = $index->find(strtolower($search));
echo "Search for \"$search\" returned " .count($hits). " hits.\n\n";

foreach ($hits as $hit) {
    echo str_repeat('-', 80) . "\n";
    echo 'ID:    ' . $hit->id                     ."\n";
    echo 'Score: ' . sprintf('%.2f', $hit->score) ."\n\n";

    foreach ($hit->getDocument()->getFieldNames() as $field) {
        echo "$field: \n";
        echo '    ' . trim(substr($hit->$field,0,76)) . "\n";
    }
}
