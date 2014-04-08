<?php

if (!session::checkAccessFromModuleIni('locales_allow')){
    return;
}

if (isset($_POST)) { 
    html::specialEncode ($_POST);
}

date_default_timezone_set(config::getMainIni('date_default_timezone'));

echo lang::translate('Current date and time according to setup');
echo "<br />";
echo strftime(config::getMainIni('date_format_long'));

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
