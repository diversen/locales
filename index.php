<?php

if (!session::checkAccessControl('locales_allow')){
    return;
}

if (isset($_POST)) html::specialEncode ($_POST);

locales::displaySetTimezone();
locales::displaySetLocaleUTF8();
locales::displaySetLanguage();
