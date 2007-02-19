<?php
require_once('../library/gettext/gettext.inc');

$encoding = 'UTF-8';
$language = 'fr_FR';
T_setlocale(LC_MESSAGES, $language.'.'.$encoding);
if (!locale_emulation()) {
    printf(T_("Local %s is not supported by the system.")."<br>", $language);
}
putenv("LANG=$language.$encoding");
putenv("LANGUAGE=$language.$encoding");
bindtextdomain("messages", "../locale");
bind_textdomain_codeset("messages", "UTF-8");
textdomain("messages");

echo T_("Welcome to USVN");
echo"<br><br>";
echo T_("Copyright USVN");
