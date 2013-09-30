<?php

/**
 * file for creating gallery module menu
 *
 * @package    gallery
 */
$_MODULE_MENU = array (
    array(
        'title' => lang::translate('Base settings'),
        'url' => '/gallery/index'),

        
);

$locales_db = config::getModuleIni('locales_db_language');
if ($locales_db) {
    $_MODULE_MENU[] = array(
        'title' => lang::translate('Edit translation'),
        'url' => '/gallery/admin/index',
        'auth' => 'admin');
}
if (config::getModuleIni('gallery_allow_zip')) {
    $_MODULE_MENU[] =
        array(
            'title' => lang::translate('gallery_menu_upload_zip'),
            'url' => '/gallery/upload/index',
            'auth' => 'admin');
}