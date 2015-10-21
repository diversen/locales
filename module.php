<?php

namespace modules\locales;

use diversen\cache;
use diversen\conf;
use diversen\html;
use diversen\http;
use diversen\intl;
use diversen\lang;
use diversen\moduleloader;
use diversen\session;
//use diversen\strings\normalize;
//use diversen\uri;

/**
 * File containing file for settings locales
 * @package locales
 */

use modules\configdb\module as configdb;
use modules\locales\views as locales_views;

moduleloader::includeModule('configdb');


/**
 * Class for setting locales
 * @package locales
 */
class module {

    /**
     * displays a form for setting default timezone
     */
    public static function displaySetTimezone() {
        if (isset($_POST['timezone'])) {
            if (intl::validTimezone($_POST['timezone'])) {
                configdb::set('date_default_timezone', $_POST['timezone'], 'main');
                session::setActionMessage(lang::translate('Timezone has been updated'));
                http::locationHeader("/locales/index");
            } else {
                session::setActionMessage(lang::translate('Timezone is not valid'));
            }
        }

        $default = conf::getMainIni('date_default_timezone');
        self::setTimezoneForm($default);
    }

    /**
     * /locales/index action
     * @return void
     */
    public function indexAction() {

        if (!session::checkAccessFromModuleIni('locales_allow')) {
            return;
        }

        if (isset($_POST)) {
            html::specialEncode($_POST);
        }

        // if user is logged in ensure to display system wide timezone
        date_default_timezone_set(conf::getMainIni('date_default_timezone'));
        echo locales_views::timezoneInfo();

        self::displaySetTimezone();
        if (!conf::isWindows()) {
            // we can only set locales from web
            self::displaySetLocaleUTF8();
        }

        if (isset($_POST['language'])) {
            self::updateLanguage();
        }

        $default = conf::getMainIni('language');
        self::displaySetLanguage($default);

    }

    /**
     * displays dropdown view timezone selection
     * @param string $default
     */
    public static function setTimezoneForm($default = null) {
        $dropdown = intl::getTimezones();

        html::formStart('timezone_form');
        html::legend(lang::translate('Set timezone for your system'));
        html::select('timezone', $dropdown, 'zone', 'id', $default, array(), null);
        html::submit('submit', lang::translate('Submit'));
        html::formEnd();

        echo html::getStr();
    }

    /**
     * displays a form for setting default UTF8 locales
     */
    public static function displaySetLocaleUTF8() {
        if (isset($_POST['locale'])) {
            if (intl::validLocaleUTF8($_POST['locale'])) {
                configdb::set('locale', $_POST['locale'], 'main');
                session::setActionMessage(lang::translate('Locale has been updated'));
                http::locationHeader("/locales/index");
            } else {
                session::setActionMessage(lang::translate('Locale is not valid'));
            }
        }

        // if we can not get locales return
        $dropdown = intl::getSystemLocalesUTF8();
        if (!$dropdown) {
            return;
        }

        $default = intl::getLocale();

        html::formStart('locale');
        html::legend(lang::translate('Set locale. Set e.g. dates and money symbols to your language specifics'));
        html::select('locale', $dropdown, 'locale', 'id', $default, array(), null);
        html::submit('submit', lang::translate('Submit'));
        html::formEnd();

        echo html::getStr();
    }

    /**
     * method for getting system translations (languages)
     * @return array $rows rows with system languages for populating dropdown
     */
    public static function getLanguagesForDropdown() {

        $languages = conf::getModuleIni('locales_languages');
        foreach ($languages as $val) {
            $ary[] = array('id' => $val, 'language' => $val);
        }
        return $ary;
    }

    /**
     * method for checking if system translation (language) exists 
     * @param string $language the language to check for
     * @return boolean true if language exists else false
     */
    public static function validLanguage($language) {
        $langs = self::getLanguagesForDropdown();
        foreach ($langs as $key => $val) {
            if ($val['id'] == $language) {
                return true;
            }
        }
        return false;
    }

    /**
     * updates a language with the configdb module
     * @param string $redirect
     */
    public static function updateLanguage($redirect = '/locales/index') {
        if (self::validLanguage($_POST['language'])) {
            // set interface language
            configdb::set('language', $_POST['language'], 'main');

            // set html lang ="" attr
            $lang = str_replace('_', '-', $_POST['language']);
            configdb::set('lang', $lang, 'main');

            session::setActionMessage(lang::translate('Locale has been updated'));
            http::locationHeader($redirect);
        } else {
            session::setActionMessage(lang::translate('Language is not valid'));
        }
    }

    /**
     * updates a language per account
     * this is placed in system_cache with the following uniqids 
     *                  ('account_locales_language', {user_id})
     *                  ('account_locales_lang', {user_id})
     * @param string $redirect
     */
    public static function updateAccountLanguage($redirect = '/locales/index') {
        if (self::validLanguage($_POST['language'])) {
            cache::set('account_locales_language', session::getUserId(), $_POST['language']);
            $lang = self::getHtmlLanguageCode($_POST['language']);
            cache::set('account_locales_lang', session::getUserId(), $lang);
            session::setActionMessage(lang::translate('Locale has been updated'));
            http::locationHeader($redirect);
        } else {
            session::setActionMessage(lang::translate('Language is not valid'));
        }
    }

    /**
     * updates a language per account
     * this is placed in system_cache with the following uniqids 
     *                  ('account_locales_language', {user_id})
     *                  ('account_locales_lang', {user_id})
     * @param string $redirect
     */
    public static function updateAccountTimezone($redirect = '/locales/index') {
        if (intl::validTimezone($_POST['timezone'])) {
            cache::set('account_timezone', session::getUserId(), $_POST['timezone']);
            session::setActionMessage(lang::translate('Timezone has been updated'));
            http::locationHeader($redirect);
        } else {
            session::setActionMessage(lang::translate('Timezone is not valid'));
        }
    }

    /**
     * transforms e.g. da_DK to da-DK
     * @param type $language
     * @return type
     */
    public static function getHtmlLanguageCode($language) {
        return str_replace('_', '-', $language);
    }

    /**
     * method for displaying a form for setting system translation (language)
     */
    public static function displaySetLanguage($default) {
        $dropdown = self::getLanguagesForDropdown();
        html::formStart('language');
        html::legend(lang::translate('Set language of interface and HTML document'));
        html::select('language', $dropdown, 'language', 'id', $default, array(), null);
        html::submit('submit', lang::translate('Submit'));
        html::formEnd();
        echo html::getStr();
    }


}
