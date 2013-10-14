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

if (isset($_POST['language'])) {
    locales::updateLanguage();
}

$default = config::getMainIni('language');
locales::displaySetLanguage($default);
locales::displayReloadLang();
