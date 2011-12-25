<?php

if (!session::checkAccessControl('locales_allow')){
    return;
}


locales::displaySetTimezone();

locales::displaySetLocaleUTF8();

locales::displaySetLanguage();