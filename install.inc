<?php

/**
 * installer file for content module
 *
 * @package   language
 */

$_INSTALL['MAIN_MENU_ITEM'] = array (
    'title' => 'locales_main_menu_title',
    'url' => '/locales/index',
    'auth' => 'admin',
    'module_name' => 'locales',
    'parent' => 0,
    'weight' => 3,
    'admin_only' => true,
    'section' => 'settings'
);


$_INSTALL['VERSION'] = 1.81;

$_INSTALL['PUBLIC_CLONE_URL'] = 'git://github.com/diversen/locales.git';
$_INSTALL['PRIVATE_CLONE_URL'] = 'git@github.com:diversen/locales.git';
