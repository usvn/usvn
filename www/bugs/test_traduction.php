<?php
require_once 'Zend/Translate.php';
require_once 'USVN/Translation.php';

USVN_Translation::initTranslation("en_US", "locale");
echo USVN_Translation::_('Delete') . "\n";
