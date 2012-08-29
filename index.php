<?php

if (!session::checkAccessControl('locales_allow')){
    return;
}

if (isset($_POST)) html::specialEncode ($_POST);

locales::displaySetTimezone();
if (!config::isWindows()) {
    // we can only set locales from web
    locales::displaySetLocaleUTF8();
}
locales::displaySetLanguage();
