<?php

if (!session::checkAccessFromModuleIni('locales_allow')){
    return;
}

if (isset($_POST)) { 
    html::specialEncode ($_POST);
}

// if user is logged in ensure to display system wide timezone
date_default_timezone_set(config::getMainIni('date_default_timezone'));
echo locales_views::timezoneInfo();

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
