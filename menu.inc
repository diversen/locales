<?php

use diversen\lang;
use diversen\conf;
/**
 * file for creating gallery module menu
 *
 * @package    gallery
 */
$_MODULE_MENU = array (
    array(
        'title' => lang::translate('Base settings'),
        'url' => '/locales/index'),

        
);

$locales_db = conf::getModuleIni('locales_db_language');
if ($locales_db) {
    $_MODULE_MENU[] = array(
        'title' => lang::translate('Edit translation'),
        'url' => '/locales/edit',
        'auth' => 'admin');
}
