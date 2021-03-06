<?php

use diversen\conf;
use diversen\lang;
/**
 * installer file for content module
 *
 * @package   language
 */

$_INSTALL['MAIN_MENU_ITEM'] = array (
    'title' => lang::translate('Language and time', null, array ('no_translate' => true)),
    'url' => '/locales/index',
    'auth' => conf::getModuleIni('locales_allow'),
    'module_name' => 'locales',
    'parent' => 0,
    'weight' => 3,
    'admin_only' => true,
    'section' => 'settings'
);

